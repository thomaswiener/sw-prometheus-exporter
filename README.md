# Shopware Prometheus Exporter

This plugin exposes a new route under /store-api/metrics with shopware specific metrics in the format of
[prometheus](https://prometheus.io). 

## Installation

Install the plugin via composer by running:

```
composer require thomaswiener/sw-prometheus-exporter
```

## Prometheus Configuration

After installing the plugin, your Prometheus needs to get pointed to your Magento Metrics endpoint. To do so,
add the following lines to your prometheus.yml under scrape_configs:

``` yaml
- job_name: 'Shopware Exporter'
  scrape_interval: 5m
  scrape_timeout: 60s
  metrics_path: /store-api/metrics
  static_configs:
  - targets: 
    - your-shopware-url
```

## Metrics

The following metrics will be collected:

| Metric                               | Labels                          | TYPE    | Help                                |
|:-------------------------------------|:--------------------------------|:--------|:------------------------------------|
| shopware_orders_count_total          | status, store_code              | gauge   | All Shopware Orders                 |
| shopware_orders_amount_total         | status, store_code              | gauge   | Total amount of all Shopware Orders |
| shopware_customer_count_total        | store_code                      | gauge   | Total count of available customer   |

## Add you own Metric

1. To add a new metric, you need to implement the `Wienerio\ShopwarePrometheusExporter\Services\Metric`. 
   Or extend the `Wienerio\ShopwarePrometheusExporter\Services\AbstractMetric`.
2. Tag you service with `sw.prometheus.metric`
```
Wienerio\ShopwarePrometheusExporter\Services\Metric\OrdersCountTotal:
  arguments:
    - '@Doctrine\DBAL\Connection'
    - '@logger'
  tags: ['sw.prometheus.metric']     
```

Your metric should just respond with an instance of Transport\Metric. Each Metric contains one or more MetricValues.

## Contribution

If you have something to contribute, weither it's a feature, a feature request, an issue or something else, feel free
to. There are no contribution guidelines.
