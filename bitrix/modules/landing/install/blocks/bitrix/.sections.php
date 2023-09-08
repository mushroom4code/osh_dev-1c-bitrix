<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(
	\Bitrix\Landing\Manager::getDocRoot() .
	'/bitrix/modules/landing/blocks/.sections.php'
);


return array(
	'last' => Loc::getMessage('LD_BLOCK_SECTION_LAST'),
	'recommended' =>[
		'name' => Loc::getMessage('LD_BLOCK_SECTION_RECOMMENDED'),
		'type' => ['knowledge']
	],
	'cover' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_COVER'),
		'meta' => [
			'ai_text_placeholder' => 'write a beautiful selling phrase for the site, sell flowers, local shop',
			'ai_text_assistant_text' => 'advertising headline',
		],
	],

	'text' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_TEXT_NEW'),
		'meta' => [
			'ai_text_placeholder' => 'text for website, family business, sale of flowers',
			'ai_text_max_tokens' => 150,
		],
	],
	'text_image' => Loc::getMessage('LD_BLOCK_SECTION_TEXT_IMAGE'),
	'image' => Loc::getMessage('LD_BLOCK_SECTION_IMAGE_NEW'),
	'video' => Loc::getMessage('LD_BLOCK_SECTION_VIDEO'),

	'title' => Loc::getMessage('LD_BLOCK_SECTION_TITLE'),
	'columns' => Loc::getMessage('LD_BLOCK_SECTION_COLUMNS'),

	'tiles' => Loc::getMessage('LD_BLOCK_SECTION_TILES_NEW2'),
	'countdowns' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_COUNTDOWNS'),
		'type' => ['page', 'store', 'smn']
	],

	'separator' => Loc::getMessage('LD_BLOCK_SECTION_TRANSITIONS_SEPARATORS'),

	'menu' => Loc::getMessage('LD_BLOCK_SECTION_MENU_NEW'),
	'sidebar' => Loc::getMessage('LD_BLOCK_SECTION_SIDEBAR'),
	'footer' => Loc::getMessage('LD_BLOCK_SECTION_FOOTER'),

	'forms' => Loc::getMessage('LD_BLOCK_SECTION_FORMS'),
	'news' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_NEWS'),
		'meta' => [
			'ai_text_placeholder' => 'write the news on the site, flower shop, new supply of roses',
			'ai_text_assistant_text' => 'positive news',
			'ai_text_max_tokens' => 150,
		],
	],
	'schedule' => Loc::getMessage('LD_BLOCK_SECTION_SCHEDULE'),

	'store' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_STORE_NEW'),
		'type' => ['store']
	],

	'team' => Loc::getMessage('LD_BLOCK_SECTION_TEAM'),
	'feedback' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_FEEDBACK'),
		'type' => ['page', 'store', 'smn'],
		'meta' => [
			'ai_text_placeholder' => 'write reviews, flower shop',
			'ai_text_assistant_text' => 'positive reviews',
		],
	],
	'steps' => Loc::getMessage('LD_BLOCK_SECTION_STEPS'),
	'tariffs' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_TARIFFS'),
		'type' => ['page', 'store', 'smn']
	],
	'partners' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_PARTNERS'),
		'type' => ['page', 'store', 'smn']
	],
	'about' => [
		'name' => Loc::getMessage('LD_BLOCK_SECTION_ABOUT'),
		'type' => ['page', 'store', 'smn'],
		'meta' => [
			'ai_text_placeholder' => 'write a text about our company, sell flowers, a family business',
			'ai_text_max_tokens' => 150,
		],
	],
	'contacts' => Loc::getMessage('LD_BLOCK_SECTION_CONTACTS'),
	'social' => Loc::getMessage('LD_BLOCK_SECTION_SOCIAL'),

	'other' => Loc::getMessage('LD_BLOCK_SECTION_OTHER'),
);