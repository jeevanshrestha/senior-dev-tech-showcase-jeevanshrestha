<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Helper;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use HarveyNorman\Promotional\Api\ElasticSearchConstantsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class ElasticsearchHelper implements ElasticSearchConstantsInterface
{
    private Client $client;
    private ScopeConfigInterface $scopeConfig;
    private LoggerInterface $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;

        $host = $this->scopeConfig->getValue(
            'catalog/search/elasticsearch7_server_hostname',
            ScopeInterface::SCOPE_STORE
        ) ?? 'localhost';

        $port = $this->scopeConfig->getValue(
            'catalog/search/elasticsearch7_server_port',
            ScopeInterface::SCOPE_STORE
        ) ?? '9200';

        $esHost = "http://{$host}:{$port}";

        $this->client = ClientBuilder::create()
            ->setHosts([$esHost])
            ->setRetries(2)
            ->build();

        $this->logger->info("ElasticsearchHelper initialized with host: {$esHost}");
    }

    /**
     * Create or update the Elasticsearch index template
     */
    public function createIndexTemplate(): void
    {
        $this->logger->info('Starting to create Elasticsearch index template: promotional_index_template');

        $params = [
            'name' =>  self::INDEX_TEMPLATE_NAME,
            'body' => [
                'index_patterns' => ['promotional_*'],
                'template' => [
                    'mappings' => [
                        'properties' => [
                            'id'=>['type' => 'integer'],
                            'sku' => ['type' => 'keyword'],
                            'name' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256]
                                ]
                            ],
                            'price' => ['type' => 'float'],
                            'discount_percentage' => ['type' => 'float'],
                            'start_date' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'],
                            'end_date' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis'],
                            'promotion_status' => ['type' => 'boolean'],
                            'isPromotionEligible' => ['type' => 'boolean'],
                            'discounted_price' => ['type' => 'float'],
                        ]
                    ]
                ]
            ]
        ];

        try {
            $this->client->indices()->putIndexTemplate($params);
            $this->logger->info('Elasticsearch index template created successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to create Elasticsearch index template: ' . $e->getMessage());
        }
    }

    /**
     * Create the Elasticsearch index 'promotional_index_1'
     */
    public function createIndex(): void
    {
        $this->logger->info('Starting to create Elasticsearch index: promotional_index_1');

        $params = [
            'index' => self::INDEX_NAME ,
            'body' => []
        ];

        try {
            if (!$this->client->indices()->exists(['index' => self::INDEX_NAME])) {
                $this->client->indices()->create($params);
                $this->logger->info('Elasticsearch index promotional_index_1 created successfully.');
            } else {
                $this->logger->info('Elasticsearch index promotional_index_1 already exists.');
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to create Elasticsearch index: ' . $e->getMessage());
        }
    }

    /**
     * Upsert a document into 'promotional_index_1' with given ID and data
     */
    public function upsertDocument(string $id, array $data): void
    {
        $params = [
            'index' =>  self::INDEX_NAME ,
            'id' => $id,
            'body' => [
                'doc' => $data,
                'doc_as_upsert' => true,
            ],
        ];

        $this->client->update($params);
    }

    /**
     * Delete a document from Elasticsearch index by ID
     */
    public function deleteDocumentById(string $id): void
    {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $id,
        ];

        // Check if document exists before deleting to avoid errors
        if ($this->client->exists($params)) {
            $this->client->delete($params);
        }
    }

    /**
     * Perform a multi_match search in Elasticsearch and return matching document IDs.
     *
     * @param string $query Search query string
     * @param int $size Number of results to return
     * @return array List of matching document IDs
     */
    public function search(string $query, int $size = 50): array
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => self::SEARCHABLE_FIELDS,
                        'type' => 'most_fields',
                        'fuzziness' => 2,
                        'tie_breaker' => 0.2,
                    ],
                ],
                'size' => $size,
                '_source' => false,
            ],
        ];

        try {
            $response = $this->client->search($params);
            $hits = $response['hits']['hits'] ?? [];

            // Extract document IDs
            $ids = array_map(fn($hit) => $hit['_id'] ?? null, $hits);

            return array_filter($ids); // Remove nulls if any
        } catch (\Exception $e) {
            $this->logger->error('Elasticsearch search failed: ' . $e->getMessage());
            return [];
        }
    }


}
