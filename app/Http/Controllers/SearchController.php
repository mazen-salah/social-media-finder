<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GoogleSearchResults;
use Illuminate\Support\Facades\Response;

class SearchController extends Controller
{
    public function index()
    {
        return view('search');
    }

    public function search(Request $request)
{
    $validated = $request->validate([
        'keywords' => 'required|string',
        'location' => 'nullable|string',
        'platform' => 'required|string|in:facebook,instagram,linkedin,twitter,tiktok',
        'page' => 'nullable|integer|min:1',
        'apiKey' => 'required|string'
    ]);

    $platforms = [
        'facebook' => 'site:facebook.com',
        'instagram' => 'site:instagram.com',
        'linkedin' => 'site:linkedin.com/in',
        'twitter' => 'site:x.com OR site:twitter.com',
        'tiktok' => 'site:tiktok.com'
    ];

    $locationPattern = "\"lives in {location}\" OR \"from {location}\" OR \"{location}\"";
    $siteDork = $platforms[$validated['platform']];
    $keywords = implode(" AND ", array_filter(array_map('trim', explode(',', $validated['keywords'])), fn($keyword) => !empty($keyword)));

    $location = '';
    if ($validated['location']) {
        if ($validated['platform'] === 'facebook') {
            $location = str_replace('{location}', $validated['location'], $locationPattern);
        } else {
            $location = $validated['location'];
        }
    }

    $query = "{$siteDork} ({$keywords}) {$location}";
    $start = (($validated['page'] ?? 1) - 1) * 10;

    // Use SerpAPI with API key from request
    $search = new GoogleSearchResults($validated['apiKey']);
    $params = [
        "q" => $query,
        "start" => $start,
        "num" => 10,
        "hl" => "en"
    ];
    $results = json_decode(json_encode($search->get_json($params)), true);

    $totalCount = $results['search_information']['total_results'] ?? 0;
    $links = array_column($results['organic_results'] ?? [], 'link');

    return view('results', [
        'results' => $links,
        'query' => $query,
        'page' => $validated['page'] ?? 1,
        'keywords' => $validated['keywords'],
        'location' => $validated['location'],
        'platform' => $validated['platform'],
        'totalCount' => $totalCount
    ]);
}

    public function autocomplete(Request $request)
    {
        $query = strtolower($request->query('query'));

        $suggestions = [
            // Occupations
            'developer',
            'artist',
            'influencer',
            'business',
            'marketer',
            'designer',
            'photographer',
            'content creator',
            'writer',
            'musician',
            'actor',
            'entrepreneur',
            'investor',
            'blogger',
            'coach',
            'trainer',
            'podcaster',
            'consultant',
            'teacher',
            'youtuber',
            'streamer',
            'public speaker',
            'freelancer',
            'graphic designer',
            'software engineer',
            'UX/UI designer',
            'digital marketer',
            'ecommerce expert',
            'fitness trainer',
            'nutritionist',
            'fashion designer',
            'makeup artist',
            'gamer',
            'journalist',
            'cinematographer',
            'producer',

            // Industries
            'technology',
            'finance',
            'healthcare',
            'education',
            'real estate',
            'automotive',
            'entertainment',
            'hospitality',
            'gaming',
            'advertising',
            'media',
            'sports',
            'manufacturing',
            'logistics',
            'consulting',
            'legal services',
            'retail',
            'food industry',
            'travel',
            'pharmaceuticals',
            'cybersecurity',
            'telecommunications',

            // Skills
            'coding',
            'SEO',
            'copywriting',
            'video editing',
            'graphic design',
            'web development',
            'data analysis',
            'marketing strategy',
            'content marketing',
            'social media management',
            'branding',
            'project management',
            'business strategy',
            'public speaking',
            'negotiation',
            'sales',
            'networking',
            'lead generation',

            // Hobbies & Interests
            'photography',
            'music',
            'fashion',
            'fitness',
            'yoga',
            'cooking',
            'traveling',
            'hiking',
            'sports',
            'video games',
            'reading',
            'writing',
            'art',
            'DIY',
            'dance',
            'cars',
            'motorcycles',
            'gardening',
            'technology trends',
            'startups',
            'entrepreneurship',
            'personal development',
            'self-improvement',
            'meditation',

            // Communities & Groups
            'startup founders',
            'women in tech',
            'freelancers',
            'remote workers',
            'digital nomads',
            'crypto enthusiasts',
            'climate activists',
            'mental health advocates',
            'pet lovers',
            'book club members',
            'anime fans',
            'cosplayers',
            'language learners',
            'fitness enthusiasts',
            'vegan community',
            'minimalists',
            'luxury lifestyle',
            'small business owners',
            'nonprofit organizations',
            'environmentalists'
        ];

        // Split query by commas and get the latest term
        $terms = array_map('trim', explode(',', $query));
        $latestTerm = end($terms); // Get the last entered term

        // Filter suggestions based on the latest term
        $matches = array_filter($suggestions, fn($s) => str_starts_with($s, $latestTerm));

        return response()->json(array_values($matches));
    }
}
