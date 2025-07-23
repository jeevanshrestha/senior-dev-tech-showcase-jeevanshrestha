## Instruction Manual

Module: HarveyNorman_Promotional

This document provides step-by-step instructions for installing, configuring, testing, and uninstalling the **HarveyNorman_Promotional** Magento 2 module. The module integrates with RabbitMQ for message queuing and Elasticsearch for product indexing, enabling advanced promotional product management.

### Navigation

After successful installation and configuration, you can access the promotional products at `/promotional/product`.

### Assumption and Environment

- **PHP 8.2** is installed and configured.
- **Magento 2.4.6** is installed and running.
- **Elasticsearch 7.x** is installed and accessible from Magento.
- A dedicated promotional products index may be created and managed by the module.
- **RabbitMQ** is installed and running.
- Magento is already configured to use RabbitMQ as its message broker (queue/amqp).
- Magento cron job is configured and running.

### Install

Copy the module to the `app/code/` directory and run the following commands to enable and set up the module:

```code
php bin/magento module:enable HarveyNorman_Promotional
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
php bin/magento cache:flush
```

### Test RabbitMQ Publisher with Command Line

Verify RabbitMQ publishing by running:

```code
bin/magento harveynorman:promotional:publish-test
```

### Run RabbitMQ Consumer with Command Line

Start the consumer to process promotional messages:

```code
bin/magento queue:consumers:start harveynorman.promotional.consumer
```
Check `system.log` for consumed test messages.

### Configure RabbitMQ Consumer CRON Runner

Add the following configuration to your `.env` file to enable the consumer runner via cron:

```code 
'cron_consumers_runner' => [
    'cron_run' => true,
    'max_messages' => 100,
    'consumers' => [
        'harveynorman.promotional.consumer'
    ]
],
```

### Unit Test Configuration

Set up the unit test suite for the module in `phpunit.xml`:

```code
<testsuites>
    <testsuite name="HarveyNorman Promotional Tests">
        <directory>app/code/HarveyNorman/Promotional/Test/Unit</directory>
    </testsuite>
</testsuites>
```
Run the unit tests for the suite:

```code
php vendor/bin/phpunit -c phpunit.xml --testsuite "HarveyNorman Promotional Tests"
```

### Elasticsearch Configuration

The module automatically creates an Elasticsearch index template and index during setup. Check `system.log` for details:

```code 
[2025-07-22T04:23:41.505419+00:00] main.INFO: ElasticsearchHelper initialized with host: http://localhost:9200 [] []
[2025-07-22T04:23:41.506193+00:00] main.INFO: Starting to create Elasticsearch index template: promotional_index_template [] []
[2025-07-22T04:23:41.550871+00:00] main.INFO: Elasticsearch index template created successfully. [] []
[2025-07-22T04:23:41.551073+00:00] main.INFO: Starting to create Elasticsearch index: promotional_index_1 [] []
[2025-07-22T04:23:41.720830+00:00] main.INFO: Elasticsearch index promotional_index_1 created successfully. [] []
```

If the Elasticsearch index and template are not created during setup, use the CLI to configure them:

```code 
php bin/magento promotional:elasticsearch:setup
Starting Elasticsearch setup...
Index template created or already exists.
Index created or already exists.
Elasticsearch setup finished successfully.
```

To add existing products to the Elasticsearch index, run:

```code 
bin/magento promotional:products:reindex 
Found 44 promotional products to queue for upsert...
Queued product ID 1 (U1KY24FR)
Queued product ID 2 (ZMTB4LGY)
Queued product ID 3 (J4G3SN7R)
Queued product ID 4 (9DOHZ8QW)
Queued product ID 5 (43TXPB9J)
Queued product ID 6 (0NPO56WX)
Queued product ID 7 (5KYX7CSO)
Queued product ID 8 (L23HPY4Z)
Queued product ID 9 (58NQ1UVR)
```

### Uninstall Module

To remove the module, run:

```code
php bin/magento module:disable HarveyNorman_Promotional

# MySQL Console
DELETE FROM setup_module WHERE module = 'HarveyNorman_Promotional';
DROP TABLE IF EXISTS harveynorman_promotional_product;
```

