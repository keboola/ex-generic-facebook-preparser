<?php
namespace Keboola\ExGenericModule;

use Keboola\GenericExtractor\Modules\ResponseModuleInterface;
use Keboola\Juicer\Config\JobConfig;
use Keboola\Juicer\Exception\UserException;
use Keboola\Utils\Utils;

class FacebookPreParser implements ResponseModuleInterface
{
    /**
     * @return array
     */
    public function process($response, JobConfig $jobConfig)
    {
        if (empty($jobConfig->getConfig()['parseObject'])) {
            return $response;
        }

        $config = $jobConfig->getConfig()['parseObject'];

        if (!is_object($response)) {
            if (empty($response)) {
                return [];
            }

            throw new UserException("Data in response is not an object, while one was expected!");
        }

        $path = empty($config['path'])
            ? "."
            : $config['path'];


        $result = [];

        foreach (Utils::getDataFromPath($path, $response, '.') as $metric) {
            if ($metric->values && is_array($metric->values)) {
                $parsedMetric = [];
                foreach($metric->values as $value) {
                    if (!$value->end_time) {
                        continue;
                    }
                    // scalar value or empty value
                    if (!isset($value->value) || is_scalar($value->value)) {
                        if (!isset($value->value)) {
                            $val = 0;
                        } else {
                            $val = $value->value;
                        }
                        $parsedMetric[] = (object) [
                            "key1" => "",
                            "key2" => "",
                            "end_time" => $value->end_time,
                            "value" => $val 
                        ];                        
                    }
                    if (is_object($value->value)) {
                        foreach((array)$value->value as $key1 => $value1) {
                            if (is_object($value1)) {
                                foreach((array) $value1 as $key2 => $value2) {
                                    $parsedMetric[] = (object) [
                                        "key1" => $key1,
                                        "key2" => $key2,
                                        "end_time" => $value->end_time,
                                        "value" => $value2
                                    ];
                                }
                            } else {
                                $parsedMetric[] = (object) [
                                    "key1" => $key1,
                                    "key2" => "",
                                    "end_time" => $value->end_time,
                                    "value" => $value1
                                ];
                            }
                        }
                    } 
                }
                $metric->values = (object) $parsedMetric;
            }
            $result[] = $metric;
        }
        return (object) $result;
    }

    protected function convertToKeyValue() {}

}
