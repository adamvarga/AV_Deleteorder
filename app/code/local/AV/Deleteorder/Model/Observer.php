<?php

class AV_Deleteorder_Model_Observer {

    public function deleteOrder() {
        Mage::app('admin')->setUseSessionInUrl(false);
        $config = Mage::getStoreConfig('av_deleteorder/general/order');
        $order_list = str_replace(' ', '', explode(',', $config));
        if (!$config) {
            $msg_error_empty = Mage::helper('av_deleteorder')->__('Please fill the order id field!');
            Mage::getSingleton("core/session")->addError($msg_error_empty);
            return;
        }
        $orders = array();
        foreach ($order_list as $order_item) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($order_item);
            $increment_id = $order->getIncrementId();
            if ($increment_id == NULL) {
                $msg_error_nf = Mage::helper('av_deleteorder')->__('Order # %s not found', $order_item);
                Mage::getSingleton("core/session")->addError($msg_error_nf);
            } else {
                $orders[] = $order;
            }
        }
        try {
            if ($orders) {
                foreach ($orders as $_order) {
                    Mage::getModel('sales/order')->loadByIncrementId($_order->getIncrementId())->delete();
                    $msg_success = Mage::helper('av_deleteorder')->__('Order # %s is removed, See more details in order-delete.log', $_order->getIncrementId());
                    Mage::getSingleton("core/session")->addSuccess($msg_success);
                    Mage::log("Order #" . $_order->getIncrementId() . " is removed", null, "order-delete.log");
                }
            }
        } catch (Exception $e) {
            $msg_error = Mage::helper('av_deleteorder')->__('Order # %s could not be removed, See more details in order-delete.log', $_order->getIncrementId());
            Mage::getSingleton("core/session")->addError($msg_error);
            Mage::log("Order #" . $_order->getIncrementId() . " could not be remvoved: " . $e->getMessage(), null, "order-delete.log");
        }
    }

}
