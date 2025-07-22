# Senior Tech Showcase

## Requirement Analysis
 
### Overview

The Magento 2 module facilitates the management and display of promotional products. This module includes full CRUD capabilities, frontend listing and widget display, integration with RabbitMQ for asynchronous updates, and Elasticsearch for high-performance product searching and indexing.

---

### Admin Panel Features

- Add a new **"Promotional Products"** menu item under **Marketing**.

- **Grid View**:
    - Display promotional products with the following columns: `ID`, `SKU`, `Name`, `Price`, and `Promotion Status`.

- **CRUD Functionality**:
    - Create, Read, Update, and Delete promotional products.
    - Editable fields:
        - `sku`
        - `name`
        - `price`
        - `discount_percentage`
        - `start_date`
        - `end_date`
        - `promotion_status`
- **Mass Actions**:
    - Mass enable/disable promotional products.

---

### Frontend Display Requirements

- **Promotional Product List Page**:
    - Displays all promotional products in a grid or list view.
- **Search Page Integration**:
    - Enable full-text search of promotional products using Elasticsearch.
- **CMS Widget**:
    - Create a Magento widget to embed promotional products on any CMS page or block.

---

### Unit Test Coverage Expectations

Unit tests must be written to ensure coverage of core module functionalities:
- **Discount Price Calculation**:
    - Verify discount logic is accurately applied to base price.
- **Promotion Validity**:
    - Ensure correct validation of `start_date` and `end_date`.
- **Eligibility Logic**:
    - Check whether products are considered eligible based on `promotion_status` and date ranges.

---

### RabbitMQ Integration Specifications

- **Producer**:
    - On admin changes (add/update/delete promotional products), send a product message to the `promotional_product_update` queue.
- **Consumer**:
    - Process incoming product messages and update Elasticsearch accordingly.
- **Manual Command**:
    - Implement a CLI command to trigger the RabbitMQ queue manually for batch updates.

### Example Payload Structure

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

---

### Elasticsearch Integration Specifications

- **Indexing**:
    - Promotional products should be stored in an Elasticsearch index with fields for full product details.
- **Search**:
    - Implement a search helper to support fulltext and faceted search over promotional products.
- **Re-indexing**:
    - Trigger re-index operations via RabbitMQ consumer or manually.

---
