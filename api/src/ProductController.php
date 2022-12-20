<?php

class ProductController
{

  function __construct(public ProductGateway $gateway)
  {
  }
  /**
   * Undocumented function
   *
   * @param string $method
   * @param string | null $id
   * @return void
   */
  function processRequest(string $method, $id): void
  {
    if ($id) {
      $this->processResourceRequest($method, $id);
    } else {
      $this->processCollectionRequest($method);
    }
  }

  function processResourceRequest(string $method, string $id): void
  {

    $elem = $this->gateway->get($id);
    if (!$elem) {
      http_response_code(404);
      echo json_encode(['message' => 'Product not found.']);
    }

    switch ($method) {
      case 'GET':
        echo json_encode($elem);
        break;
      case 'PATCH':
        parse_str(file_get_contents('php://input'), $_PATCH);
        $errors = $this->getValidationErrors($_PATCH);
        if (!empty($errors)) {
          // response code for: unprocessable entity
          http_response_code(422);
          echo json_encode(['errors' =>  $errors]);
          break;
        }

        $modified_row_count = $this->gateway->update($id, $_PATCH);
        // response code for: new entity created
        http_response_code(201);
        echo json_encode(['message' => "Product $id updated", 'modified rows count' => $modified_row_count]);
        break;
      case 'DELETE':
        $modified_row_count = $this->gateway->delete($id);
        echo json_encode(['message' => "Product $id deleted", 'modified rows count' => $modified_row_count]);
        break;
      default:
        // response code for: method not allowed
        http_response_code(405);
        header('Allow: GET, PATCH, DELETE');
    }
  }

  function processCollectionRequest(string $method): void
  {

    switch ($method) {
      case 'GET':
        $allProducts = $this->gateway->getAll();
        echo json_encode($allProducts);
        break;
      case 'POST':
        $errors = $this->getValidationErrors();
        if (!empty($errors)) {
          // response code for: unprocessable entity
          http_response_code(422);
          echo json_encode(['errors' =>  $errors]);
          break;
        }
        $id = $this->gateway->create();
        // response code for: new entity created
        http_response_code(201);
        echo json_encode(['message' => 'Product created', 'ID' => $id]);
        break;
      default:
        // response code for: method not allowed
        http_response_code(405);
        header('Allow: GET, POST');
    }
  }

  /**
   * Undocumented function
   *
   * @param mixed[] | null | false $data
   * @return string[]
   */
  function getValidationErrors($data = null)
  {
    $data = $data ? $data : $_POST;
    $errors = [];
    $has_name = isset($data['name']);
    if (!$has_name) {
      $errors[] = 'Valid name is required';
    }

    $has_size = isset($data['size']);
    if (!$has_size) {
      $errors[] = 'Valid size is required';
    }

    if ($has_size) {
      $size = filter_var($data['size'], FILTER_VALIDATE_INT);
      if ($size === false) {
        $errors[] = 'Valid size is required';
      }
    }

    $has_available = isset($data['is_available']);
    if (!$has_available) {
      $errors[] = 'Valid is_available is required';
    }

    if ($has_available) {
      $has_available = filter_var($data['is_available'], FILTER_VALIDATE_BOOL);
      if ($has_available === false && ($data['is_available'] !== 'false' && $data['is_available'] !== 'true')) {
        $errors[] = 'Valid is_available is required';
      }
    }

    return $errors;
  }
}
