<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages('template.php');


function showOrderBlock($listStatuses, $accountNumber)
{
    global $arBasketItems;
    foreach ($accountNumber as $key => $order) {

        $classStatus = '';

        if ($order['STATUS_ID'] === 'N') {
            $classStatus = 'status_pending_payment bg-yellowSt text-black';
        } else if ($order['STATUS_ID'] === 'P') {
            $classStatus = 'status_payment_yes ';
        } else if ($order['STATUS_ID'] === 'F') {
            $classStatus = 'status_completed bg-greenLight dark:bg-greenButton text-white';
        } ?>
        <div class="flex flex-col bg-textDark dark:bg-darkBox rounded-3xl p-5 mx-0 mb-5 sale-order-list-inner-container relative">
            <div class="sale-order-list-title-container">
                <h3 class="my-2 title-orders-his">
                    <p class="mb-3 xl:text-2xl lg:text-lg text-md font-medium text-textLight dark:text-textDarkLightGray">
                        <?= Loc::getMessage('SPOL_TPL_ORDER') ?>
                        <?= Loc::getMessage('SPOL_TPL_NUMBER_SIGN') . $order['ACCOUNT_NUMBER'] ?>
                        <span class="text-xs font-normal text-textLight dark:text-textDarkLightGray">
                            <?= Loc::getMessage('SPOL_TPL_FROM_DATE') ?>&nbsp<?= $order['DATE_INSERT_FORMAT'] ?>
                        </span>
                    </p>
                    <div class="absolute top-0 right-0 py-2 px-4 rounded-bl-3xl rounded-tr-3xl max-w-[250px]
                        md:min-w-[200px] w-full flex items-center justify-center <?= $classStatus ?>">
                        <span class="text-xs font-semibold"><?= htmlspecialcharsbx($listStatuses['STATUS'][$order['STATUS_ID']]['NAME']) ?></span>
                    </div>
                </h3>
            </div>
            <div class="my-3 flex flex-row overflow-auto">
                <?php foreach ($order['PICTURE'] as $url) {
                    $url = !empty($url) ? $url : '/local/templates/Oshisha/images/no-photo.gif'; ?>
                    <img class="image_box_orders mr-3 rounded-md p-3 bg-white xl:max-w-32 max-w-28 object-container
                     xl:max-h-32 max-h-28" src="<?= $url ?>" alt="orderImage"/>
                <?php } ?>
            </div>
            <div class="col pt-3 wrap-order-l">
                <div class="sale-order-list-inner-row sale-order-list-inner-row sale-order-list-wrap md:items-center
                 items-end flex md:flex-row flex-col justify-between">
                    <div class="sale-order-list-inner mr-3 md:mb-0 mb-3">
                        <div class="sale-order-list-inner-row-body">
                            <div class="sale-order-list-payment">
                                <div class="sale-order-list-payment-price flex flex-row">
                                    <span class="sale-order-list-payment-element mr-2 font-semibold dark:font-medium
                                     text-textLight dark:text-textDarkLightGray">Итого: </span>
                                    <span class="sale-order-list-payment-number text-md font-semibold dark:font-medium
                                    text-textLight dark:text-textDarkLightGray"><?= round($order['PRICE']) . ' ₽' ?> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sale-order-list-inner-row flex flex-row">
                        <div class=" sale-order-list-repeat-container mr-3">
                            <a class="sale-order-list-repeat-link md:px-5 px-3 md:py-3 py-2 dark:bg-dark-red rounded-md bg-light-red
                             dark:shadow-md shadow-shadowDark w-full ark:hover:bg-hoverRedDark cursor-pointer flex items-center
                            <?= $order['IS_NOT_ACTIVE_ITEMS_PRESENT'] === true ? 'js--not-active' : '' ?>"
                               href="/personal/orders/?COPY_ORDER=Y&ID=<?= $order['ACCOUNT_NUMBER'] ?>">
                                <svg viewBox="0 0 19 22" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" class="mr-2 md:w-7 w-4 md:h-7 h-4">
                                    <path d="M9.49743 0.666656V3.63808C4.7569 3.63808 0.893433 7.64088 0.893433 12.5524C0.893433 17.4639 4.7569 21.4667 9.49743 21.4667C14.238 21.4667 18.1014 17.4639 18.1014 12.5524C18.1014 10.6011 17.485 8.79763 16.4527 7.32722L15.0897 8.73942C15.7835 9.83239 16.1894 11.1409 16.1894 12.5524C16.1894 16.3933 13.2046 19.4857 9.49743 19.4857C5.79022 19.4857 2.80543 16.3933 2.80543 12.5524C2.80543 8.71146 5.79022 5.61904 9.49743 5.61904V8.59047L14.2774 4.62856L9.49743 0.666656Z"
                                          fill="white"/>
                                </svg>
                                <span class="text-white md:text-md text-xs">
                                    <?= Loc::getMessage('SPOL_TPL_REPEAT_ORDER') ?>
                                </span>
                            </a>
                            <div id="popup_mess_order_copy"></div>
                        </div>
                        <div class=" sale-order-list-about-container">
                            <a class="sale-order-list-about-link md:px-5 px-3 md:py-3 py-2 dark:shadow-md w-full shadow-shadowDark
                            dark:hover:bg-black cursor-pointer dark:bg-grayButton rounded-md bg-lightGrayBg flex items-center"
                               href="/personal/orders/<?= $order['ACCOUNT_NUMBER'] ?>/">
                                <svg class="md:w-7 w-5 md:h-7 h-5" viewBox="0 0 25 26" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5636 8.51667C13.372 8.51667 14.0272 7.83764 14.0272 7C14.0272 6.16237 13.372 5.48334 12.5636 5.48334C11.7553 5.48334 11.1 6.16237 11.1 7C11.1 7.83764 11.7553 8.51667 12.5636 8.51667Z"
                                          fill="white"/>
                                    <path d="M12.5636 14.5833C13.372 14.5833 14.0272 13.9043 14.0272 13.0667C14.0272 12.229 13.372 11.55 12.5636 11.55C11.7553 11.55 11.1 12.229 11.1 13.0667C11.1 13.9043 11.7553 14.5833 12.5636 14.5833Z"
                                          fill="white"/>
                                    <path d="M12.5636 20.65C13.372 20.65 14.0272 19.971 14.0272 19.1334C14.0272 18.2957 13.372 17.6167 12.5636 17.6167C11.7553 17.6167 11.1 18.2957 11.1 19.1334C11.1 19.971 11.7553 20.65 12.5636 20.65Z"
                                          fill="white"/>
                                </svg>
                                <span class="text-white md:text-md text-xs">
                                    Подробности заказа
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}