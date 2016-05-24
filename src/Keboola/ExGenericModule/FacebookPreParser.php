<?php
namespace Keboola\ExGenericModule;

use Keboola\GenericExtractor\Modules\ResponseModuleInterface;
use Keboola\Juicer\Config\JobConfig;
use Keboola\Utils\Utils;

class FacebookPreParser implements ResponseModuleInterface
{
    /**
     * @param $response
     * @param JobConfig $jobConfig
     * @return array
     */
    public function process($response, JobConfig $jobConfig)
    {

        $config = $jobConfig->getConfig();
        if (!isset($config['parser']['method'])) {
            return $response;
        }

        $path = empty($config['dataField'])
            ? "."
            : $config['dataField'];

        if ($config['parser']['method'] == 'facebook.insights') {
            return $this->flatten($path, $response);
        }

        return $response;
    }

    /**
     *
     * Creates key value pairs for `values` property up to 2 leves of nesting
     *
     * @param $path
     * @param $data
     * @return array
     * @throws \Keboola\Utils\Exception\NoDataFoundException
     */
    protected function flatten($path, $data) {
        foreach (Utils::getDataFromPath($path, $data, '.') as $metric) {
            if ($metric->values && is_array($metric->values)) {
                $parsedMetrics = [];
                foreach($metric->values as $value) {

                    $end_time = '';
                    if (isset($value->end_time)) {
                        $end_time = $value->end_time;
                    }
                    if (!isset($value->end_time) && $metric->period != 'lifetime') {
                        continue;
                    }
                    // scalar value or empty value
                    if (!isset($value->value) || is_scalar($value->value)) {
                        if (!isset($value->value)) {
                            $val = 0;
                        } else {
                            $val = $value->value;
                        }
                        $parsedMetrics[] = (object) [
                            "id" => $metric->id,
                            "key1" => "",
                            "key2" => "",
                            "end_time" => $end_time,
                            "value" => $val
                        ];
                        continue;
                    }
                    if (is_object($value->value)) {
                        foreach((array)$value->value as $key1 => $value1) {
                            if (is_object($value1)) {
                                foreach((array) $value1 as $key2 => $value2) {
                                    $parsedMetrics[] = (object) [
                                        "id" => $metric->id,
                                        "key1" => $key1,
                                        "key2" => $key2,
                                        "end_time" => $end_time,
                                        "value" => $value2
                                    ];
                                }
                            } else {
                                $parsedMetrics[] = (object) [
                                    "id" => $metric->id,
                                    "key1" => $key1,
                                    "key2" => "",
                                    "end_time" => $end_time,
                                    "value" => $value1
                                ];
                            }
                        }
                        continue;
                    }
                }
                $metric->values = $parsedMetrics;
            }
            $result[] = $metric;
        }
        if ($path != '.') {
            $data->$path =  $result;
        } else {
            $data = $result;
        }
        return $data;
    }
}
