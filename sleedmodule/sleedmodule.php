<?php

if (!defined('_PS_VERSION_')) {
  exit;
}

class SleedModule extends Module {

  private $db;

  public function __construct() {
    $this->name = 'sleedmodule';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Dimitris Verakis';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    $this->bootstrap = true;
    $this->db = Db::getInstance();

    parent::__construct();

    $this->displayName = $this->l('Sleed V2 Module');
    $this->description = $this->l('New feature for edit product page.');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    if (!Configuration::get('MYMODULE_NAME'))
      $this->warning = $this->l('No name provided');
  }

  public function install() {
    if (Shop::isFeatureActive())
      Shop::setContext(Shop::CONTEXT_ALL);

    $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'product_edit` (
                                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `feature1` VARCHAR(255) NULL,
                                `feature2` VARCHAR(255) NULL,
                                `feature3` VARCHAR(255) NULL,
                                `id_product` INT(10) UNSIGNED NOT NULL,
                                PRIMARY KEY (`id`),
                                FOREIGN KEY (`id_product`) REFERENCES `' . _DB_PREFIX_ . 'product`(`id_product`) ON DELETE CASCADE
                                ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

    if (!$this->db->execute($query))
      return false;

    if (
        !parent::install() ||
        !$this->registerHook('actionProductUpdate') ||
        !$this->registerHook('footerProduct') ||
        !$this->registerHook('displayAdminProductsExtra') ||
        !Configuration::updateValue('MYMODULE_NAME', 'sleed')
    ) {
      return false;
    }

    return true;
  }

  public function uninstall() {
    if (!parent::uninstall() || !Configuration::deleteByName('MYMODULE_NAME'))
      return false;

    $query = 'DROP TABLE `' . _DB_PREFIX_ . 'product_edit`;';
    if (!$this->db->execute($query))
      return false;

    return true;
  }

  public function hookDisplayAdminProductsExtra($params) {
    $id_product = (int)Tools::getValue('id_product');
    $data = $this->getProductFeatures($id_product);
    $this->context->smarty->assign($data);
    return $this->display(__FILE__, 'sleedmodule.tpl');
  }

  public function hookActionProductUpdate($params) {
    $id_product = (int)Tools::getValue('id_product');
    // get features, escape them, trim spaces from start and end of string and assign them to appropriate variables
    for ($i = 1; $i <= 3; $i++) {
      ${"feature$i"} = trim($this->db->escape(Tools::getValue("feature$i")));
    }

    $featuresEmpty = empty($feature1) && empty($feature2) && empty($feature3);
    // if features are empty don't insert the product into the product_edit table
    // if it is already in the table, with data from a previous insert or update, delete it
    $query = "SELECT id from `" . _DB_PREFIX_ . "product_edit` WHERE id_product = '$id_product';";
    $queryResult = $this->db->getValue($query);
    if ($queryResult && $featuresEmpty) {
      $query = "DELETE FROM `" . _DB_PREFIX_ . "product_edit` WHERE id_product = '$id_product';";
    } elseif ($queryResult && !$featuresEmpty) {
      $query = "UPDATE `" . _DB_PREFIX_ . "product_edit` SET feature1 = '$feature1', feature2 = '$feature2', feature3 = '$feature3' WHERE `id_product` = '$id_product';";
    } elseif (!$queryResult && !$featuresEmpty) {
      $query = "INSERT INTO `" . _DB_PREFIX_ . "product_edit` (feature1 ,feature2, feature3, id_product) VALUES ('$feature1', '$feature2', '$feature3', '$id_product');";
    } else {
      return false;
    }

    if (!$this->db->execute($query))
      return false;
  }

  public function hookDisplayFooterProduct($params) {
    $id_product = (int)Tools::getValue('id_product');
    $data = $this->getProductFeatures($id_product);
    $this->context->smarty->assign($data);
    return $this->display(__FILE__, 'sleedmoduleproduct.tpl');
  }

  public function getProductFeatures($id_product) {
    $data = [];
    $query = "SELECT `feature1`, `feature2`, `feature3` FROM `" . _DB_PREFIX_ . "product_edit` WHERE `id_product` = '$id_product';";
    $queryResult = $this->db->executeS($query);
    if (!empty($queryResult))
      $data = $queryResult[0];

    return $data;
  }
}
