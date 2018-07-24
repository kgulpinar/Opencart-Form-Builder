<?php
class ModelPagePageBuildTable extends Model {
	public function Buildtable() {
		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."page_request'");
		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form` (
				  `page_form_id` int(11) NOT NULL AUTO_INCREMENT,
				  `show_guest` int(11) NOT NULL,
				  `status` tinyint(4) NOT NULL,
				  `customer_email_status` tinyint(4) NOT NULL,
				  `admin_email_status` tinyint(4) NOT NULL,
				  `sort_order` int(11) NOT NULL,
				  `top` tinyint(4) NOT NULL,
				  `bottom` tinyint(4) NOT NULL,
				  `captcha` tinyint(4) NOT NULL,
				  PRIMARY KEY (`page_form_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_customer_group` (
			  `page_form_id` int(11) NOT NULL,
			  `customer_group_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`page_form_id`,`customer_group_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_description` (
			  `page_form_id` int(11) NOT NULL,
			  `admin_subject` varchar(255) NOT NULL,
			  `admin_message` text NOT NULL,
			  `customer_subject` varchar(255) NOT NULL,
			  `customer_message` text NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `description` text NOT NULL,
			  `meta_title` varchar(255) NOT NULL,
			  `meta_description` text NOT NULL,
			  `meta_keyword` text NOT NULL,
			  `success_title` varchar(255) NOT NULL,
			  `success_description` text NOT NULL,
			  `fieldset_title` varchar(255) NOT NULL,
			  `submit_button` varchar(255) NOT NULL,
			  `guest_error` varchar(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_option` (
			  `page_form_option_id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_form_id` int(11) NOT NULL,
			  `required` tinyint(4) NOT NULL,
			  `type` varchar(255) NOT NULL,
			  `sort_order` int(11) NOT NULL,
			  PRIMARY KEY (`page_form_option_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_option_description` (
			  `page_form_id` int(11) NOT NULL,
			  `page_form_option_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `field_name` varchar(255) NOT NULL,
			  `field_value` varchar(255) NOT NULL,
			  `field_error` varchar(255) NOT NULL,
			  `field_placeholder` varchar(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_option_value` (
			  `page_form_option_value_id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_form_option_id` int(11) NOT NULL,
			  `page_form_id` int(11) NOT NULL,
			  `sort_order` int(3) NOT NULL,
			  PRIMARY KEY (`page_form_option_value_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_option_value_description` (`page_form_option_value_id` int(11) NOT NULL, `page_form_option_id` int(11) NOT NULL, `page_form_id` int(11) NOT NULL, `language_id` int(11) NOT NULL, `name` varchar(128) NOT NULL, PRIMARY KEY (`page_form_option_value_id`,`page_form_option_id`,`page_form_id`,`language_id`), KEY `name` (`name`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_store` (
			  `page_form_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`page_form_id`,`store_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_request` (
			  `page_request_id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_form_id` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  `firstname` varchar(255) NOT NULL,
			  `lastname` varchar(255) NOT NULL,
			  `customer_group_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `user_agent` varchar(255) NOT NULL,
			  `page_form_title` varchar(255) NOT NULL,
			  `ip` varchar(40) NOT NULL,
			  `date_added` datetime NOT NULL,
			  PRIMARY KEY (`page_request_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_request_option` (
			  `page_request_option_id` int(11) NOT NULL AUTO_INCREMENT,
			  `page_request_id` int(11) NOT NULL,
			  `page_form_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `value` text NOT NULL,
			  `type` varchar(32) NOT NULL,
			  PRIMARY KEY (`page_request_option_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");
		}

		// Information
		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."page_form_information'");
		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."page_form_information` (
			  `page_form_id` int(11) NOT NULL,
			  `information_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`page_form_id`,`information_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		}

		// Page Form Option
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."page_form_option` WHERE `Field` = 'status'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."page_form_option` ADD `status` TINYINT NOT NULL AFTER `type`");
		}

		// Description
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."page_form_description` WHERE `Field` = 'bottom_description'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."page_form_description` ADD `bottom_description` text NOT NULL AFTER `description`");
		}

		// Description
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."page_form_option_description` WHERE `Field` = 'field_help'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."page_form_option_description` ADD `field_help` text NOT NULL AFTER `field_name`");
		}

		// CSS
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."page_form` WHERE `Field` = 'css'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."page_form` ADD `css` text NOT NULL AFTER `status`");
		}

		// Admin Email
		$query = $this->db->query("SHOW COLUMNS FROM `". DB_PREFIX ."page_form` WHERE `Field` = 'admin_email'");
		if(!$query->num_rows) {
			$this->db->query("ALTER TABLE `". DB_PREFIX ."page_form` ADD `admin_email` varchar(96) NOT NULL AFTER `status`");
		}
	}
}