<?php

class TextProcessingService {

    public static function fetchGitHubRepositories($subject) {
        $subject = self::preprocessSubject($subject);
        $keywords = array_filter(explode(' ', $subject));
        $queryString = implode('+', $keywords);

        $apiUrl = 'https://api.github.com/search/repositories';
        $queryParams = http_build_query([
            'q' => $queryString,
            'sort' => 'stars',
            'order' => 'desc',
        ]);
        $url = "{$apiUrl}?{$queryParams}";

        error_log("GitHub API Query: " . $url);

        $options = [
            'http' => [
                'header' => "User-Agent: ResourceFinderBot",
                'method' => 'GET',
            ],
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        error_log("GitHub API Response: " . $response);

        if ($response === false) {
            return [];
        }

        $data = json_decode($response, true);
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (isset($item['description']) && strlen($item['description']) > 200) {
                    $item['description'] = substr($item['description'], 0, 197) . '...';
                }
            }
            return $data['items'];
        } else {
            return [];
        }
    }

    public static function preprocessSubject($subject) {
        $stopWords = ['și', 'să', 'în', 'la', 'pe', 'cu', 'un', 'o', 'de', 'ce', 'este', 'am', 'a', 'the', 'and', 'of', 'to', 'with', 'for', 'in', 'on', 'at', 'by'];

        $words = array_filter(explode(' ', preg_replace('/[^\w\s]/u', '', $subject)), function($word) use ($stopWords) {
            return !in_array(mb_strtolower($word), $stopWords);
        });

        return implode(' ', $words);
    }

    public static function fetchWebResults($subject) {
        $subject = self::preprocessSubject($subject);
        $queryString = urlencode($subject);

        $url = "https://www.google.com/search?q={$queryString}";

        error_log("Google Query URL: " . $url);

        $options = [
            'http' => [
                'header' => "User-Agent: ResourceFinderBot",
                'method' => 'GET',
            ],
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            error_log("Failed to load Google results.");
            return [];
        }

        error_log("Google Search Response: " . substr($response, 0, 1000)); 

        $results = [];
        $doc = new DOMDocument();
        @$doc->loadHTML($response);
        $xpath = new DOMXPath($doc);

        foreach ($xpath->query('//div[@class="g"]') as $result) {
            $titleElement = $xpath->query('.//h3', $result)->item(0);
            $linkElement = $xpath->query('.//a', $result)->item(0);

            if ($titleElement && $linkElement) {
                $title = $titleElement->nodeValue;
                $href = $linkElement->getAttribute('href');
                if (strpos($href, '/url?q=') === 0) {
                    $href = explode('&', substr($href, 7))[0];
                }

                error_log("Found result: " . $title . " - " . $href);
                $results[] = ['title' => $title, 'link' => $href];
            } else {
                error_log("Title or link element not found.");
            }
        }

        if (empty($results)) {
            error_log("No results found.");
        }

        return $results;
    }
}
?>
