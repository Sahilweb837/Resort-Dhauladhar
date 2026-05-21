<?php
// includes/google-reviews.php

// =========================================================
// GOOGLE PLACES API CONFIGURATION
// Add your Google Places API Key below to enable live reviews
// =========================================================
define('GOOGLE_PLACES_API_KEY', ''); // <--- ENTER YOUR API KEY HERE
define('GOOGLE_PLACE_ID', 'ChIJ6w0XpB3pOT4Ri24yF7v94j8'); // Dharamshala Resort Place ID

function fetchLiveGoogleReviews($limit = 5) {
    $apiKey = GOOGLE_PLACES_API_KEY;
    $placeId = GOOGLE_PLACE_ID;
    
    
    if (empty($apiKey)) {
        return [];
    }

    $cacheFile = __DIR__ . '/cache/google-reviews.json';
    $cacheTime = 3600 * 12; // Cache for 12 hours to save API quotas
    
    $reviews = [];
    $fetchFromApi = true;

    // Check if cache exists and is fresh
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
        if (is_array($cachedData) && !empty($cachedData['reviews'])) {
            $reviews = $cachedData['reviews'];
            $fetchFromApi = false;
        }
    }
    
    // Fetch new reviews from Google Places API
    if ($fetchFromApi) {
        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$placeId}&fields=reviews,rating,user_ratings_total&key={$apiKey}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['result']['reviews'])) {
                $reviews = $data['result']['reviews'];
                
                // Cache the data alongside the stats
                $cachePayload = [
                    'reviews' => $reviews,
                    'rating' => $data['result']['rating'] ?? 5,
                    'total' => $data['result']['user_ratings_total'] ?? 0
                ];
                file_put_contents($cacheFile, json_encode($cachePayload));
            }
        }
    }
    
    if (empty($reviews)) {
        return [];
    }

    // Format the Google API reviews to perfectly match our expected DB structure
    $formattedReviews = [];
    foreach (array_slice($reviews, 0, $limit) as $apiReview) {
        $formattedReviews[] = [
            'reviewer_name' => $apiReview['author_name'] ?? 'Google User',
            'reviewer_image' => $apiReview['profile_photo_url'] ?? '',
            'rating' => $apiReview['rating'] ?? 5,
            'review_text' => $apiReview['text'] ?? '',
            'review_date' => isset($apiReview['time']) ? date('Y-m-d', $apiReview['time']) : date('Y-m-d'),
            'review_source' => 'Google API',
            'guest_type' => 'Verified Google Review'
        ];
    }
    
    return $formattedReviews;
}

function fetchLiveGoogleStats() {
    $cacheFile = __DIR__ . '/cache/google-reviews.json';
    if (file_exists($cacheFile)) {
        $cachedData = json_decode(file_get_contents($cacheFile), true);
        if (isset($cachedData['rating']) && isset($cachedData['total'])) {
            return [
                'avg_rating' => $cachedData['rating'],
                'total' => $cachedData['total']
            ];
        }
    }
    return null;
}
?>