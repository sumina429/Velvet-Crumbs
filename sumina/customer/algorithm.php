<?php
/**
 * Project-II Algorithm Module
 * Hybrid Recommendation Algorithm for product ranking.
 */
function get_recommended_products(mysqli $db, int $uid, int $limit = 6): array
{
    // 1) Category preference score from user's purchase history
    $pref = [];
    $stmt = $db->prepare(
        "SELECT p.category, SUM(oi.quantity) AS qty
         FROM orders o
         JOIN order_items oi ON oi.oid = o.oid
         JOIN products p ON p.pid = oi.pid
         WHERE o.uid = ?
         GROUP BY p.category"
    );
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    $pref_total = 0.0;
    while ($r = $res->fetch_assoc()) {
        $cat = (string) $r['category'];
        $qty = (float) ($r['qty'] ?? 0);
        $pref[$cat] = $qty;
        $pref_total += $qty;
    }
    $stmt->close();

    // 2) Average purchase price for user
    $avg_price = null;
    $stmt = $db->prepare(
        "SELECT AVG(p.price) AS avg_price
         FROM orders o
         JOIN order_items oi ON oi.oid = o.oid
         JOIN products p ON p.pid = oi.pid
         WHERE o.uid = ?"
    );
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $avg_price = $row['avg_price'] !== null ? (float) $row['avg_price'] : null;
    }
    $stmt->close();

    // 3) Global trending score by sold quantity
    $trend = [];
    $stmt = $db->prepare(
        "SELECT p.pid, SUM(oi.quantity) AS sold_qty
         FROM order_items oi
         JOIN orders o ON o.oid = oi.oid
         JOIN products p ON p.pid = oi.pid
         GROUP BY p.pid"
    );
    $stmt->execute();
    $res = $stmt->get_result();
    $max_trend = 0.0;
    while ($r = $res->fetch_assoc()) {
        $pid = (int) $r['pid'];
        $qty = (float) ($r['sold_qty'] ?? 0);
        $trend[$pid] = $qty;
        if ($qty > $max_trend) {
            $max_trend = $qty;
        }
    }
    $stmt->close();

    // 4) Exclude already purchased products
    $purchased = [];
    $stmt = $db->prepare(
        "SELECT DISTINCT oi.pid
         FROM orders o
         JOIN order_items oi ON oi.oid = o.oid
         WHERE o.uid = ?"
    );
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $purchased[(int) $r['pid']] = true;
    }
    $stmt->close();

    // 5) Build candidate list
    $candidates = [];
    $res = $db->query("SELECT * FROM products");
    if ($res) {
        while ($p = $res->fetch_assoc()) {
            if (isset($purchased[(int) $p['pid']])) {
                continue;
            }
            $candidates[] = $p;
        }
    }

    // New user fallback: pure trending ranking
    if ($pref_total <= 0) {
        foreach ($candidates as &$p) {
            $pid = (int) $p['pid'];
            $t = $trend[$pid] ?? 0.0;
            $p['_score'] = $max_trend > 0 ? ($t / $max_trend) : 0.0;
        }
        unset($p);
        usort($candidates, fn($a, $b) => ($b['_score'] <=> $a['_score']));
        return array_slice($candidates, 0, $limit);
    }

    // Hybrid scoring
    foreach ($candidates as &$p) {
        $pid = (int) $p['pid'];
        $cat = (string) ($p['category'] ?? '');

        $pref_score = isset($pref[$cat]) ? ($pref[$cat] / $pref_total) : 0.0;
        $trend_score = $max_trend > 0 ? (($trend[$pid] ?? 0.0) / $max_trend) : 0.0;

        $price_score = 0.0;
        if ($avg_price !== null && isset($p['price'])) {
            $price = (float) $p['price'];
            $diff = abs($price - $avg_price);
            $price_score = 1.0 / (1.0 + ($diff / max(1.0, $avg_price)));
        }

        $p['_score'] = (0.60 * $pref_score) + (0.30 * $trend_score) + (0.10 * $price_score);
    }
    unset($p);

    usort($candidates, fn($a, $b) => ($b['_score'] <=> $a['_score']));
    return array_slice($candidates, 0, $limit);
}

