<?php

use Keboola\Juicer\Config\JobConfig,
    Keboola\Juicer\Exception\UserException;

class FacebookPreParserTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessFlat()
    {
        $cfg = JobConfig::create([
            'endpoint' => '/insights',
            'parseObject' => [
                'path' => 'data'
            ]
        ]);

        $module = new \Keboola\ExGenericModule\FacebookPreParser();

        $jsonResponse = <<<JSON
{
   "data": [
      {
         "name": "page_fan_adds_unique",
         "period": "day",
         "values": [
            {
               "value": 1,
               "end_time": "2016-05-15T07:00:00+0000"
            },
            {
               "value": 2,
               "end_time": "2016-05-16T07:00:00+0000"
            }
         ],
         "title": "Daily New Likes",
         "id": "177057932317550/insights/page_fan_adds_unique/day"
      }
   ],
   "paging": {
      "previous": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1462889963&until=1463149163",
      "next": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1463408363&until=1463667563"
   }
}
JSON;


        $response = json_decode($jsonResponse);

        $data = $module->process($response, $cfg);
        $this->assertEquals((object) [
                (object) [
                'name' => 'page_fan_adds_unique',
                'period' => 'day',
                'values' => (object) [
                    (object) ['key1' => '', 'key2' => '', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 1 ],
                    (object) ['key1' => '', 'key2' => '', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 2 ],
                ],
                'title' => 'Daily New Likes',
                'id' => '177057932317550/insights/page_fan_adds_unique/day'
            ]
        ], $data);
    }

    public function testProcessNested1Level()
    {
        $cfg = JobConfig::create([
            'endpoint' => '/insights',
            'parseObject' => [
                'path' => 'data'
            ]
        ]);

        $module = new \Keboola\ExGenericModule\FacebookPreParser();

        $jsonResponse = <<<JSON
{
   "data": [
      {
         "name": "page_impressions_by_story_type",
         "period": "days_28",
         "values": [
            {
               "value": {
                  "fan": 269,
                  "mention": 567,
                  "other": 0
               },
               "end_time": "2016-05-15T07:00:00+0000"
            },
            {
               "value": {
                  "fan": 270,
                  "mention": 565,
                  "other": 0
               },
               "end_time": "2016-05-16T07:00:00+0000"
            }
         ],
         "title": "28 Days Viral Impressions By Story Type",
         "id": "177057932317550/insights/page_impressions_by_story_type/days_28"
      }
   ],
   "paging": {
      "previous": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1462889963&until=1463149163",
      "next": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1463408363&until=1463667563"
   }
}
JSON;


        $response = json_decode($jsonResponse);

        $data = $module->process($response, $cfg);
        $this->assertEquals((object) [
                (object) [
                'name' => 'page_impressions_by_story_type',
                'period' => 'days_28',
                'values' => (object) [
                    (object) ['key1' => 'fan', 'key2' => '', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 269 ],
                    (object) ['key1' => 'mention', 'key2' => '', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 567 ],
                    (object) ['key1' => 'other', 'key2' => '', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 0 ],
                    (object) ['key1' => 'fan', 'key2' => '', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 270 ],
                    (object) ['key1' => 'mention', 'key2' => '', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 565 ],
                    (object) ['key1' => 'other', 'key2' => '', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 0 ],
                ],
                'title' => '28 Days Viral Impressions By Story Type',
                'id' => '177057932317550/insights/page_impressions_by_story_type/days_28'
            ]
        ], $data);
    }

    public function testProcessNested2Levels()
    {
        $cfg = JobConfig::create([
            'endpoint' => '/insights',
            'parseObject' => [
                'path' => 'data'
            ]
        ]);

        $module = new \Keboola\ExGenericModule\FacebookPreParser();

        $jsonResponse = <<<JSON
{
   "data": [
      {
         "name": "page_views_by_age_gender_logged_in_unique",
         "period": "day",
         "values": [
            {
               "value": {
                  "13-17": {
                     "U": 1,
                     "F": 1,
                     "M": 1
                  },
                  "18-24": {
                     "U": 2,
                     "F": 2,
                     "M": 2
                  }
               },
               "end_time": "2016-05-15T07:00:00+0000"
            },
            {
               "value": {
                  "13-17": {
                     "U": 1,
                     "F": 2,
                     "M": 3
                  },
                  "18-24": {
                     "U": 3,
                     "F": 2,
                     "M": 1
                  }
               },
               "end_time": "2016-05-16T07:00:00+0000"
            }
         ],
         "title": "Daily Total logged-in views count per Page by age and gender",
         "id": "177057932317550/insights/page_views_by_age_gender_logged_in_unique/day"
      }
   ],
   "paging": {
      "previous": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1462889963&until=1463149163",
      "next": "https://graph.facebook.com/v2.6/177057932317550/insights?access_token=EAACEdEose0cBAIY6lSVIVZBZBZC7hAZAEtk22lHbbtEWknQEQftC9tRPpu9ARtWZCuXVlAHlNoS88ilHTHPxi5I1gf4fDXp5ozem2KCBiGompf691lZBQjpmksqZCAvLPgGz7mGX3nTQNsPhNyqCv4yOkO18asTZCsNIKaqjrrzjHwZDZD&since=1463408363&until=1463667563"
   }
}
JSON;


        $response = json_decode($jsonResponse);

        $data = $module->process($response, $cfg);
        $this->assertEquals((object) [
                (object) [
                'name' => 'page_views_by_age_gender_logged_in_unique',
                'period' => 'day',
                'values' => (object) [
                    (object) ['key1' => '13-17', 'key2' => 'U', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 1 ],
                    (object) ['key1' => '13-17', 'key2' => 'F', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 1 ],
                    (object) ['key1' => '13-17', 'key2' => 'M', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 1 ],
                    (object) ['key1' => '18-24', 'key2' => 'U', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 2 ],
                    (object) ['key1' => '18-24', 'key2' => 'F', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 2 ],
                    (object) ['key1' => '18-24', 'key2' => 'M', 'end_time' => '2016-05-15T07:00:00+0000', 'value' => 2 ],
                    (object) ['key1' => '13-17', 'key2' => 'U', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 1 ],
                    (object) ['key1' => '13-17', 'key2' => 'F', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 2 ],
                    (object) ['key1' => '13-17', 'key2' => 'M', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 3 ],
                    (object) ['key1' => '18-24', 'key2' => 'U', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 3 ],
                    (object) ['key1' => '18-24', 'key2' => 'F', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 2 ],
                    (object) ['key1' => '18-24', 'key2' => 'M', 'end_time' => '2016-05-16T07:00:00+0000', 'value' => 1 ],
                ],
                'title' => 'Daily Total logged-in views count per Page by age and gender',
                'id' => '177057932317550/insights/page_views_by_age_gender_logged_in_unique/day'
            ]
        ], $data);

    }
}
