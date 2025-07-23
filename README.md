# Senior Tech Showcase

## Overview

This exercise is designed to examine your ability to;

* Understand key requirements
* Utilise magento and third party libraries when necessary
* Implement effective tests and magento modules

## Guidelines

* Fork this repository
* Commit often
* Create a PR back to the repository once you're happy

## Tasks

* Using Magento 2.4.6 Community (https://github.com/magento/magento2/releases/tag/2.4.6-p8) as your framework, create a new Magento 2 module named "PromotionalProducts" with the following components


#### Admin

* Create a new menu item under Marketing called "Promotional Products"
* Implement a grid to display promotional products with columns for ID, SKU, Name, Price, and Promotion Status
* Add ability to edit promotional products, including fields for promotion start/end dates and discount percentage
* Implement mass actions for enabling/disabling promotions

#### Frontend

* Create a new page to display promotional products
* Implement a block and template to show promotional products in a grid or list view
* Add a widget that can be used to display promotional products on CMS pages or blocks

#### Unit Tests

Write unit tests for the core functionality of the module, including

* Testing the logic for calculating discounted prices
* Validating promotion start/end dates
* Checking if a product is eligible for promotion



#### RabbitMQ Integration

* Set up a RabbitMQ consumer to process product messages for promotions
* Implement a command to manually trigger the queue processing
* Create a producer to send product updates to the queue when changes are made in the admin panel

An example payload for Rabbit could be the following
```
{
  "product_id": 1234,
  "sku": "PROD-1234",
  "name": "Example Product",
  "promotion": {
    "id": 5678,
    "name": "Summer Sale",
    "type": "percentage_discount",
    "value": 20.00,
    "start_date": "2024-06-01T00:00:00Z",
    "end_date": "2024-08-31T23:59:59Z",
    "status": "active"
  },
  "original_price": 99.99,
  "discounted_price": 79.99,
  "stock_qty": 100,
  "categories": [10, 15, 20],
  "attributes": {
    "color": "blue",
    "size": "medium"
  },
  "visibility": ["catalog", "search"],
  "is_in_stock": true,
  "updated_at": "2024-05-15T14:30:00Z"
}
```


#### Elasticsearch Integration

* Store promotional product data in an Elasticsearch index
* Implement search functionality for promotional products using Elasticsearch



## Notes


* Try to spend less than 3 hours for the core functionality (excluding RabbitMQ integration and the Elasticsearch component) - you may cut features or spend more time.
* We are not assessing your ability to turn this around quickly - we all have lives and jobs to attend to.
* When submitting the PR, commentary is appreciated.
* For any area you choose to skip, or cannot complete, describe what you would do using psuedocode or comments in it's place




## Deliverables

#### a) Requirements Document

Provide a detailed requirements document outlining the expected functionality of the module, including:

* Admin panel features
* Frontend display requirements
* Unit test coverage expectations
* RabbitMQ integration specifications
* Elasticsearch integration specifications


#### b) Complete module code
* Installation and setup instructions
* Brief documentation explaining their approach and any assumptions made
* Unit test results
* Instructions for setting up and testing the RabbitMQ integration
* Instructions for setting up and testing the Elasticsearch integration

## Evaluation Criteria

* Code quality and adherence to Magento 2 best practices
* Completeness of implemented features
* Proper use of Magento 2 architecture and design patterns
* Unit test coverage and quality
* Documentation clarity
* Successful implementation of RabbitMQ and Elasticsearch integration
