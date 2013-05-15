<?php


class TextMessageThread extends CActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 'text_message_thread';
    }

}

?>
