<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model;

use HarveyNorman\Promotional\Api\Data\ProductInterface;
use HarveyNorman\Promotional\Api\Data\ProductInterfaceFactory;
use HarveyNorman\Promotional\Api\Data\ProductSearchResultsInterfaceFactory;
use HarveyNorman\Promotional\Api\ProductRepositoryInterface;
use HarveyNorman\Promotional\Model\ResourceModel\Product as ResourceProduct;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface; 
use Magento\Framework\Api\SearchResultsInterface;

class ProductRepository implements  ProductRepositoryInterface
{

    /**
     * @var ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Product
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ResourceProduct
     */
    protected $resource;


    /**
     * @param ResourceProduct $resource
     * @param ProductInterfaceFactory $productFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceProduct $resource,
        ProductInterfaceFactory $productFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /** 
     * @param Product $product
     * @inheritDoc
     * @throws CouldNotSaveException
     */
    public function save(ProductInterface $product): ProductInterface
    {
        try {
            $this->resource->save($product);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the product: %1',
                $exception->getMessage()
            ));
        }
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function get($productId): ProductInterface
    {
        $product = $this->productFactory->create();
        $this->resource->load($product, $productId);
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product with id "%1" does not exist.', $productId));
        }
        return $product;
    }

    /**
     * @inheritDoc
     */
    public function getList(
       SearchCriteriaInterface $criteria
    ):  SearchResultsInterface
    {
        $collection = $this->productCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(ProductInterface $product): bool
    {
        try {
            $productModel = $this->productFactory->create();
            $this->resource->load($productModel, $product->getProductId());
            $this->resource->delete($productModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Product: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($productId): bool
    {
        return $this->delete($this->get($productId));
    }

    /**
     * @inheritDoc
     */
    public function getById(int $productId): ProductInterface
    {
        $product = $this->productFactory->create();
        $this->resource->load($product, $productId);
        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product with id "%1" does not exist.', $productId));
        }
        return $product;
    }
}

