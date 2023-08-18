<?php

use Bitrix\Catalog\Integration\Report\Handler\Chart\ChartStoreInfo;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorableImplementation;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;
use Bitrix\Catalog\Integration\Report\StoreStock\StoreStockSale;
use Bitrix\Catalog\Integration\Report\Handler\Chart\StoreInfoCombiner\StoreWithProductsInfoCombiner;
use Bitrix\Currency\CurrencyManager;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

final class CatalogReportStoreStockSaleChartStoresGrid
	extends CBitrixComponent
	implements Errorable
{

	private const GRID_ID = 'catalog_report_store_sale_stores_grid';

	private $currency;

	use ErrorableImplementation;

	public function executeComponent()
	{
		$this->errorCollection = new \Bitrix\Main\ErrorCollection();
		
		if ($this->checkModules())
		{
			$this->initGrid();
			$this->includeComponentTemplate();
		}

		if ($this->hasErrors())
		{
			$this->showErrors();
		}
	}

	private function checkModules(): bool
	{
		$validateResult = true;

		if (!Loader::includeModule('catalog'))
		{
			$this->errorCollection->add([new \Bitrix\Main\Error('Module "catalog" is not installed.')]);
			$validateResult = false;
		}

		if (!Loader::includeModule('sale'))
		{
			$this->errorCollection->add([new \Bitrix\Main\Error('Module "sale" is not installed.')]);
			$validateResult = false;
		}

		if (!Loader::includeModule('currency'))
		{
			$this->errorCollection->add([new \Bitrix\Main\Error('Module "currency" is not installed.')]);
			$validateResult = false;
		}


		return $validateResult;
	}

	private function initGrid(): void
	{
		$grid = [];

		$this->initCurrency();

		$grid['ID'] = self::GRID_ID;
		$grid['COLUMNS'] = $this->getGridColumns();
		$grid['ROWS'] = $this->getGridRows();

		$this->arResult['GRID'] = $grid;
	}

	private function initCurrency(): void
	{
		if ($this->arParams['CURRENCY'])
		{
			if (CurrencyManager::isCurrencyExist($this->arParams['CURRENCY']))
			{
				$this->currency = $this->arParams['CURRENCY'];
			}
		}

		if (!isset($this->currency))
		{
			$this->currency = CurrencyManager::getBaseCurrency();
		}
	}

	private function getGridColumns(): array
	{
		$columns = [
			[
				'id' => 'STORE_NAME',
				'name' => Loc::getMessage('STORE_SALE_CHART_STORES_GRID_COLUMN_STORE_NAME'),
				'sort' => 'STORE_NAME',
				'default' => true,
			],

			[
				'id' => 'SUM_ARRIVED',
				'name' => \Bitrix\Main\Localization\Loc::getMessage('STORE_SALE_CHART_STORES_GRID_COLUMN_SUM_ARRIVED'),
				'sort' => 'SUM_ARRIVED',
				'default' => true,
			],

			[
				'id' => 'SUM_SHIPPED',
				'name' => \Bitrix\Main\Localization\Loc::getMessage('STORE_SALE_CHART_STORES_GRID_COLUMN_SUM_SHIPPED'),
				'sort' => 'SUM_SHIPPED',
				'default' => true,
			],

			[
			'id' => 'SOLD_PERCENT',
			'name' => \Bitrix\Main\Localization\Loc::getMessage('STORE_SALE_CHART_STORES_GRID_COLUMN_SOLD_PERCENT'),
			'sort' => 'SOLD_PERCENT',
			'default' => true,
		],
		];

		return $columns;
	}

	private function getGridRows(): array
	{
		$storeInfo = new ChartStoreInfo(new StoreWithProductsInfoCombiner());
		$storeInfo->accumulate('SUM_SHIPPED', ...StoreStockSale::getShippedData($this->arParams['FILTER']));
		$storeInfo->accumulate('SUM_ARRIVED', ...StoreStockSale::getArrivedData($this->arParams['FILTER']));

		$rows = [];
		foreach ($storeInfo->getCalculatedColumns() as $store)
		{
			$rows[] = [
				'data' => $this->prepareRow($store),
			];
		}

		return $rows;
	}

	private function prepareRow($columnData)
	{
		$shippedSum = $columnData['SUM_SHIPPED'];
		$arrivedSum = $columnData['SUM_ARRIVED'];

		if ((float)$shippedSum === 0.0)
		{
			$soldPercent = 0;
		}
		elseif ((float)$arrivedSum === 0.0)
		{
			$soldPercent = 100;
		}
		else
		{
			$soldPercent = ((float)$shippedSum / (float)$arrivedSum) * 100;
		}

		return [
			'STORE_NAME' => htmlspecialcharsbx($columnData['TITLE']),
			'SUM_ARRIVED' => \CCurrencyLang::CurrencyFormat($columnData['SUM_ARRIVED'], $this->currency, true) ?? 0.0,
			'SUM_SHIPPED' => \CCurrencyLang::CurrencyFormat($columnData['SUM_SHIPPED'], $this->currency, true) ?? 0.0,
			'SOLD_PERCENT' => StoreStockSale::computeSoldPercent($columnData['SUM_SHIPPED'], $columnData['SUM_ARRIVED']) . '%',
		];
	}

	/**
	 * Show all errors from errorCollection
	 */
	protected function showErrors(): void
	{
		foreach ($this->getErrors() as $error)
		{
			ShowError($error);
		}
	}
}