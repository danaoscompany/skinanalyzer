<?php

class Admin extends CI_Controller {

	public function update_zodiac_descriptions() {
		$this->db->query("DELETE FROM `zodiacs`");
		$commons = json_decode($_POST['commons'], true);
		$healths = json_decode($_POST['healths'], true);
		$deficiencies = json_decode($_POST['deficiencies'], true);
		$romances = json_decode($_POST['romances'], true);
		$finances = json_decode($_POST['finances'], true);
		$artistMen = json_decode($_POST['artist_men'], true);
		$artistWomen = json_decode($_POST['artist_women'], true);
		for ($i=0; $i<sizeof($commons); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'common',
				'description_in' => $commons[$i][0],
				'description_en' => $commons[$i][1]
			));
		}
		for ($i=0; $i<sizeof($healths); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'health',
				'description_in' => $healths[$i][0],
				'description_en' => $healths[$i][1]
			));
		}
		for ($i=0; $i<sizeof($deficiencies); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'deficiency',
				'description_in' => $deficiencies[$i][0],
				'description_en' => $deficiencies[$i][1]
			));
		}
		for ($i=0; $i<sizeof($romances); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'romance',
				'description_in' => $romances[$i][0],
				'description_en' => $romances[$i][1]
			));
		}
		for ($i=0; $i<sizeof($finances); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'finance',
				'description_in' => $finances[$i][0],
				'description_en' => $finances[$i][1]
			));
		}
		for ($i=0; $i<sizeof($artistMen); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'artist_man',
				'description_in' => $artistMen[$i][0],
				'description_en' => $artistMen[$i][1]
			));
		}
		for ($i=0; $i<sizeof($artistWomen); $i++) {
			$this->db->insert('zodiacs', array(
				'type' => 'artist_woman',
				'description_in' => $artistWomen[$i][0],
				'description_en' => $artistWomen[$i][1]
			));
		}
	}

	public function update_horoscope_icon() {
		$month = intval($_POST['month']);
		$path = $_POST['path'];
		$this->db->where('month', $month);
		$this->db->update('horoscope_imgs', array(
			'url' => $path
		));
	}

	public function login() {
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		$admins = $this->db->query("SELECT * FROM `admins` WHERE `email`='" . $email . "' AND `password`='" . $password . "'")->result_array();
		if (sizeof($admins) > 0) {
			$admin = $admins[0];
			echo json_encode(array(
				'response_code' => 1,
				'user_id' => intval($admin['id'])
			));
		} else {
			echo json_encode(array(
				'response_code' => -1
			));
		}
	}
	
	public function add() {
		$email = $_POST['email'];
		$password = $_POST['password'];
		if (sizeof($this->db->query("SELECT * FROM `admins` WHERE `email`='" . $email . "'")->result_array()) > 0) {
			echo -1;
		} else {
			$this->db->insert('admins', array(
				'email' => $email,
				'password' => $password
			));
			echo 1;
		}
	}
	
	public function edit() {
		$id = intval($_POST['id']);
		$email = $_POST['email'];
		$password = $_POST['password'];
		$emailChanged = intval($_POST['email_changed']);
		if ($emailChanged == 1) {
			if (sizeof($this->db->query("SELECT * FROM `admins` WHERE `email`='" . $email . "'")->result_array()) > 0) {
				echo -1;
			} else {
				$this->db->where('id', $id);
				$this->db->update('admins', array(
					'email' => $email,
					'password' => $password
				));
				echo 1;
			}
		} else if ($emailChanged == 0) {
			$this->db->where('id', $id);
			$this->db->update('admins', array(
				'email' => $email,
				'password' => $password
			));
			echo 1;
		}
	}

	public function get_users() {
		$adminID = intval($this->input->post('admin_id'));
		$start = intval($this->input->post('start'));
		$length = intval($this->input->post('length'));
		$users = $this->db->query("SELECT * FROM `user` WHERE `admin_id`=" . $adminID . " ORDER BY `first_name` ASC LIMIT " . $start . "," . $length)->result_array();
		for ($i=0; $i<sizeof($users); $i++) {
		}
		echo json_encode($users);
	}
	
	public function get_all_users() {
		$start = intval($this->input->post('start'));
		$length = intval($this->input->post('length'));
		$users = $this->db->query("SELECT * FROM `user` ORDER BY `first_name` ASC LIMIT " . $start . "," . $length)->result_array();
		for ($i=0; $i<sizeof($users); $i++) {
		}
		echo json_encode($users);
	}

	public function get_admins() {
		$start = intval($this->input->post('start'));
		$length = intval($this->input->post('length'));
		$admins = $this->db->query("SELECT * FROM `admins` ORDER BY `email` ASC LIMIT " . $start . "," . $length)->result_array();
		for ($i=0; $i<sizeof($admins); $i++) {
		}
		echo json_encode($admins);
	}
	
	public function update_settings() {
		$introText1In = $_POST['intro_text_1_in'];
		$introText2In = $_POST['intro_text_2_in'];
		$introText1En = $_POST['intro_text_1_en'];
		$introText2En = $_POST['intro_text_2_en'];
		$this->db->update('settings', array(
			'intro_text_1_in' => $introText1In,
			'intro_text_2_in' => $introText2In,
			'intro_text_1_en' => $introText1En,
			'intro_text_2_en' => $introText2En
		));
	}
	
	public function save_zodiac() {
		$type = $_POST['type'];
		$descriptionsIn = $_POST['descriptions_in'];
		$descriptionsEn = $_POST['descriptions_en'];
		$this->db->where('type', $type);
		$this->db->update('zodiacs', array(
			'description_in' => $descriptionsIn,
			'description_en' => $descriptionsEn
		));
		echo 1;
	}
	
	public function update_zodiac_in_descriptions() {
		$type = $_POST['type'];
		$aquarius = $_POST['aquarius'];
		$aries = $_POST['aries'];
		$cancer = $_POST['cancer'];
		$capricorn = $_POST['capricorn'];
		$gemini = $_POST['gemini'];
		$leo = $_POST['leo'];
		$libra = $_POST['libra'];
		$pisces = $_POST['pisces'];
		$sagitarius = $_POST['sagitarius'];
		$scorpio = $_POST['scorpio'];
		$taurus = $_POST['taurus'];
		$virgo = $_POST['virgo'];
		$this->db->where('type', $type . "_aquarius");
		$this->db->update('zodiacs', array(
			'description_in' => $aquarius
		));
		$this->db->where('type', $type . "_aries");
		$this->db->update('zodiacs', array(
			'description_in' => $aries
		));
		$this->db->where('type', $type . "_cancer");
		$this->db->update('zodiacs', array(
			'description_in' => $cancer
		));
		$this->db->where('type', $type . "_capricorn");
		$this->db->update('zodiacs', array(
			'description_in' => $capricorn
		));
		$this->db->where('type', $type . "_gemini");
		$this->db->update('zodiacs', array(
			'description_in' => $gemini
		));
		$this->db->where('type', $type . "_leo");
		$this->db->update('zodiacs', array(
			'description_in' => $leo
		));
		$this->db->where('type', $type . "_libra");
		$this->db->update('zodiacs', array(
			'description_in' => $libra
		));
		$this->db->where('type', $type . "_pisces");
		$this->db->update('zodiacs', array(
			'description_in' => $pisces
		));
		$this->db->where('type', $type . "_sagitarius");
		$this->db->update('zodiacs', array(
			'description_in' => $sagitarius
		));
		$this->db->where('type', $type . "_scorpio");
		$this->db->update('zodiacs', array(
			'description_in' => $scorpio
		));
		$this->db->where('type', $type . "_taurus");
		$this->db->update('zodiacs', array(
			'description_in' => $taurus
		));
		$this->db->where('type', $type . "_virgo");
		$this->db->update('zodiacs', array(
			'description_in' => $virgo
		));
	}
	
	public function update_zodiac_en_descriptions() {
		$type = $_POST['type'];
		$aquarius = $_POST['aquarius'];
		$aries = $_POST['aries'];
		$cancer = $_POST['cancer'];
		$capricorn = $_POST['capricorn'];
		$gemini = $_POST['gemini'];
		$leo = $_POST['leo'];
		$libra = $_POST['libra'];
		$pisces = $_POST['pisces'];
		$sagitarius = $_POST['sagitarius'];
		$scorpio = $_POST['scorpio'];
		$taurus = $_POST['taurus'];
		$virgo = $_POST['virgo'];
		$this->db->where('type', $type . "_aquarius");
		$this->db->update('zodiacs', array(
			'description_en' => $aquarius
		));
		$this->db->where('type', $type . "_aries");
		$this->db->update('zodiacs', array(
			'description_en' => $aries
		));
		$this->db->where('type', $type . "_cancer");
		$this->db->update('zodiacs', array(
			'description_en' => $cancer
		));
		$this->db->where('type', $type . "_capricorn");
		$this->db->update('zodiacs', array(
			'description_en' => $capricorn
		));
		$this->db->where('type', $type . "_gemini");
		$this->db->update('zodiacs', array(
			'description_en' => $gemini
		));
		$this->db->where('type', $type . "_leo");
		$this->db->update('zodiacs', array(
			'description_en' => $leo
		));
		$this->db->where('type', $type . "_libra");
		$this->db->update('zodiacs', array(
			'description_en' => $libra
		));
		$this->db->where('type', $type . "_pisces");
		$this->db->update('zodiacs', array(
			'description_en' => $pisces
		));
		$this->db->where('type', $type . "_sagitarius");
		$this->db->update('zodiacs', array(
			'description_en' => $sagitarius
		));
		$this->db->where('type', $type . "_scorpio");
		$this->db->update('zodiacs', array(
			'description_en' => $scorpio
		));
		$this->db->where('type', $type . "_taurus");
		$this->db->update('zodiacs', array(
			'description_en' => $taurus
		));
		$this->db->where('type', $type . "_virgo");
		$this->db->update('zodiacs', array(
			'description_en' => $virgo
		));
	}
	
	public function update_zodiac_in_settings() {
		$common = $_POST['common'];
		$romanceAquarius = $_POST['romance_aquarius'];
		$romanceAries = $_POST['romance_aries'];
		$romanceCancer = $_POST['romance_cancer'];
		$romanceCapricorn = $_POST['romance_capricorn'];
		$romanceGemini = $_POST['romance_gemini'];
		$romanceLeo = $_POST['romance_leo'];
		$romanceLibra = $_POST['romance_libra'];
		$romancePisces = $_POST['romance_pisces'];
		$romanceSagitarius = $_POST['romance_sagitarius'];
		$romanceScorpio = $_POST['romance_scorpio'];
		$romanceTaurus = $_POST['romance_taurus'];
		$romanceVirgo = $_POST['romance_virgo'];
		$deficiencyAquarius = $_POST['deficiency_aquarius'];
		$deficiencyAries = $_POST['deficiency_aries'];
		$deficiencyCancer = $_POST['deficiency_cancer'];
		$deficiencyCapricorn = $_POST['deficiency_capricorn'];
		$deficiencyGemini = $_POST['deficiency_gemini'];
		$deficiencyLeo = $_POST['deficiency_leo'];
		$deficiencyLibra = $_POST['deficiency_libra'];
		$deficiencyPisces = $_POST['deficiency_pisces'];
		$deficiencySagitarius = $_POST['deficiency_sagitarius'];
		$deficiencyScorpio = $_POST['deficiency_scorpio'];
		$deficiencyTaurus = $_POST['deficiency_taurus'];
		$deficiencyVirgo = $_POST['deficiency_virgo'];
		$financeAquarius = $_POST['finance_aquarius'];
		$financeAries = $_POST['finance_aries'];
		$financeCancer = $_POST['finance_cancer'];
		$financeCapricorn = $_POST['finance_capricorn'];
		$financeGemini = $_POST['finance_gemini'];
		$financeLeo = $_POST['finance_leo'];
		$financeLibra = $_POST['finance_libra'];
		$financePisces = $_POST['finance_pisces'];
		$financeSagitarius = $_POST['finance_sagitarius'];
		$financeScorpio = $_POST['finance_scorpio'];
		$financeTaurus = $_POST['finance_taurus'];
		$financeVirgo = $_POST['finance_virgo'];
		$healthAquarius = $_POST['health_aquarius'];
		$healthAries = $_POST['health_aries'];
		$healthCancer = $_POST['health_cancer'];
		$healthCapricorn = $_POST['health_capricorn'];
		$healthGemini = $_POST['health_gemini'];
		$healthLeo = $_POST['health_leo'];
		$healthLibra = $_POST['health_libra'];
		$healthPisces = $_POST['health_pisces'];
		$healthSagitarius = $_POST['health_sagitarius'];
		$healthScorpio = $_POST['health_scorpio'];
		$healthTaurus = $_POST['health_taurus'];
		$healthVirgo = $_POST['health_virgo'];
		$artistMan1 = $_POST['artist_man_1'];
		$artistMan2 = $_POST['artist_man_2'];
		$artistMan3 = $_POST['artist_man_3'];
		$artistMan4 = $_POST['artist_man_4'];
		$artistMan5 = $_POST['artist_man_5'];
		$artistMan6 = $_POST['artist_man_6'];
		$artistMan7 = $_POST['artist_man_7'];
		$artistMan8 = $_POST['artist_man_8'];
		$artistWoman1 = $_POST['artist_woman_1'];
		$artistWoman2 = $_POST['artist_woman_2'];
		$artistWoman3 = $_POST['artist_woman_3'];
		$artistWoman4 = $_POST['artist_woman_4'];
		$artistWoman5 = $_POST['artist_woman_5'];
		$artistWoman6 = $_POST['artist_woman_6'];
		$artistWoman7 = $_POST['artist_woman_7'];
		$artistWoman8 = $_POST['artist_woman_8'];
		$this->db->where('type', 'common');
		$this->db->update('zodiacs', array(
			'description_in' => $common
		));
		$this->db->where('type', 'romance_aquarius');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceAquarius
		));
		$this->db->where('type', 'romance_aries');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceAries
		));
		$this->db->where('type', 'romance_cancer');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceCancer
		));
		$this->db->where('type', 'romance_capricorn');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceCapricorn
		));
		$this->db->where('type', 'romance_gemini');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceGemini
		));
		$this->db->where('type', 'romance_leo');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceLeo
		));
		$this->db->where('type', 'romance_libra');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceLibra
		));
		$this->db->where('type', 'romance_pisces');
		$this->db->update('zodiacs', array(
			'description_in' => $romancePisces
		));
		$this->db->where('type', 'romance_sagitarius');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceSagitarius
		));
		$this->db->where('type', 'romance_scorpio');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceScorpio
		));
		$this->db->where('type', 'romance_taurus');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceTaurus
		));
		$this->db->where('type', 'romance_virgo');
		$this->db->update('zodiacs', array(
			'description_in' => $romanceVirgo
		));
		
		
		$this->db->where('type', 'deficiency_aquarius');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyAquarius
		));
		$this->db->where('type', 'deficiency_aries');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyAries
		));
		$this->db->where('type', 'deficiency_cancer');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyCancer
		));
		$this->db->where('type', 'deficiency_capricorn');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyCapricorn
		));
		$this->db->where('type', 'deficiency_gemini');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyGemini
		));
		$this->db->where('type', 'deficiency_leo');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyLeo
		));
		$this->db->where('type', 'deficiency_libra');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyLibra
		));
		$this->db->where('type', 'deficiency_pisces');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyPisces
		));
		$this->db->where('type', 'deficiency_sagitarius');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencySagitarius
		));
		$this->db->where('type', 'deficiency_scorpio');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyScorpio
		));
		$this->db->where('type', 'deficiency_taurus');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyTaurus
		));
		$this->db->where('type', 'deficiency_virgo');
		$this->db->update('zodiacs', array(
			'description_in' => $deficiencyVirgo
		));
		
		
		$this->db->where('type', 'finance_aquarius');
		$this->db->update('zodiacs', array(
			'description_in' => $financeAquarius
		));
		$this->db->where('type', 'finance_aries');
		$this->db->update('zodiacs', array(
			'description_in' => $financeAries
		));
		$this->db->where('type', 'finance_cancer');
		$this->db->update('zodiacs', array(
			'description_in' => $financeCancer
		));
		$this->db->where('type', 'finance_capricorn');
		$this->db->update('zodiacs', array(
			'description_in' => $financeCapricorn
		));
		$this->db->where('type', 'finance_gemini');
		$this->db->update('zodiacs', array(
			'description_in' => $financeGemini
		));
		$this->db->where('type', 'finance_leo');
		$this->db->update('zodiacs', array(
			'description_in' => $financeLeo
		));
		$this->db->where('type', 'finance_libra');
		$this->db->update('zodiacs', array(
			'description_in' => $financeLibra
		));
		$this->db->where('type', 'finance_pisces');
		$this->db->update('zodiacs', array(
			'description_in' => $financePisces
		));
		$this->db->where('type', 'finance_sagitarius');
		$this->db->update('zodiacs', array(
			'description_in' => $financeSagitarius
		));
		$this->db->where('type', 'finance_scorpio');
		$this->db->update('zodiacs', array(
			'description_in' => $financeScorpio
		));
		$this->db->where('type', 'finance_taurus');
		$this->db->update('zodiacs', array(
			'description_in' => $financeTaurus
		));
		$this->db->where('type', 'finance_virgo');
		$this->db->update('zodiacs', array(
			'description_in' => $financeVirgo
		));
		
		$this->db->where('type', 'health_aquarius');
		$this->db->update('zodiacs', array(
			'description_in' => $healthAquarius
		));
		$this->db->where('type', 'health_aries');
		$this->db->update('zodiacs', array(
			'description_in' => $healthAries
		));
		$this->db->where('type', 'health_cancer');
		$this->db->update('zodiacs', array(
			'description_in' => $healthCancer
		));
		$this->db->where('type', 'health_capricorn');
		$this->db->update('zodiacs', array(
			'description_in' => $healthCapricorn
		));
		$this->db->where('type', 'health_gemini');
		$this->db->update('zodiacs', array(
			'description_in' => $healthGemini
		));
		$this->db->where('type', 'health_leo');
		$this->db->update('zodiacs', array(
			'description_in' => $healthLeo
		));
		$this->db->where('type', 'health_libra');
		$this->db->update('zodiacs', array(
			'description_in' => $healthLibra
		));
		$this->db->where('type', 'health_pisces');
		$this->db->update('zodiacs', array(
			'description_in' => $healthPisces
		));
		$this->db->where('type', 'health_sagitarius');
		$this->db->update('zodiacs', array(
			'description_in' => $healthSagitarius
		));
		$this->db->where('type', 'health_scorpio');
		$this->db->update('zodiacs', array(
			'description_in' => $healthScorpio
		));
		$this->db->where('type', 'health_taurus');
		$this->db->update('zodiacs', array(
			'description_in' => $healthTaurus
		));
		$this->db->where('type', 'health_virgo');
		$this->db->update('zodiacs', array(
			'description_in' => $healthVirgo
		));
		
		
		
		$this->db->where('type', 'artist_man_1');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan1
		));
		$this->db->where('type', 'artist_man_2');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan2
		));
		$this->db->where('type', 'artist_man_3');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan3
		));
		$this->db->where('type', 'artist_man_4');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan4
		));
		$this->db->where('type', 'artist_man_5');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan5
		));
		$this->db->where('type', 'artist_man_6');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan6
		));
		$this->db->where('type', 'artist_man_7');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan7
		));
		$this->db->where('type', 'artist_man_8');
		$this->db->update('zodiacs', array(
			'description_in' => $artistMan8
		));
		
		$this->db->where('type', 'artist_woman_1');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman1
		));
		$this->db->where('type', 'artist_woman_2');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman2
		));
		$this->db->where('type', 'artist_woman_3');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman3
		));
		$this->db->where('type', 'artist_woman_4');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman4
		));
		$this->db->where('type', 'artist_woman_5');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman5
		));
		$this->db->where('type', 'artist_woman_6');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman6
		));
		$this->db->where('type', 'artist_woman_7');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman7
		));
		$this->db->where('type', 'artist_woman_8');
		$this->db->update('zodiacs', array(
			'description_in' => $artistWoman8
		));
	}
	
	public function update_zodiac_en_settings() {
		$common = $_POST['common'];
		$romanceAquarius = $_POST['romance_aquarius'];
		$romanceAries = $_POST['romance_aries'];
		$romanceCancer = $_POST['romance_cancer'];
		$romanceCapricorn = $_POST['romance_capricorn'];
		$romanceGemini = $_POST['romance_gemini'];
		$romanceLeo = $_POST['romance_leo'];
		$romanceLibra = $_POST['romance_libra'];
		$romancePisces = $_POST['romance_pisces'];
		$romanceSagitarius = $_POST['romance_sagitarius'];
		$romanceScorpio = $_POST['romance_scorpio'];
		$romanceTaurus = $_POST['romance_taurus'];
		$romanceVirgo = $_POST['romance_virgo'];
		$deficiencyAquarius = $_POST['deficiency_aquarius'];
		$deficiencyAries = $_POST['deficiency_aries'];
		$deficiencyCancer = $_POST['deficiency_cancer'];
		$deficiencyCapricorn = $_POST['deficiency_capricorn'];
		$deficiencyGemini = $_POST['deficiency_gemini'];
		$deficiencyLeo = $_POST['deficiency_leo'];
		$deficiencyLibra = $_POST['deficiency_libra'];
		$deficiencyPisces = $_POST['deficiency_pisces'];
		$deficiencySagitarius = $_POST['deficiency_sagitarius'];
		$deficiencyScorpio = $_POST['deficiency_scorpio'];
		$deficiencyTaurus = $_POST['deficiency_taurus'];
		$deficiencyVirgo = $_POST['deficiency_virgo'];
		$financeAquarius = $_POST['finance_aquarius'];
		$financeAries = $_POST['finance_aries'];
		$financeCancer = $_POST['finance_cancer'];
		$financeCapricorn = $_POST['finance_capricorn'];
		$financeGemini = $_POST['finance_gemini'];
		$financeLeo = $_POST['finance_leo'];
		$financeLibra = $_POST['finance_libra'];
		$financePisces = $_POST['finance_pisces'];
		$financeSagitarius = $_POST['finance_sagitarius'];
		$financeScorpio = $_POST['finance_scorpio'];
		$financeTaurus = $_POST['finance_taurus'];
		$financeVirgo = $_POST['finance_virgo'];
		$healthAquarius = $_POST['health_aquarius'];
		$healthAries = $_POST['health_aries'];
		$healthCancer = $_POST['health_cancer'];
		$healthCapricorn = $_POST['health_capricorn'];
		$healthGemini = $_POST['health_gemini'];
		$healthLeo = $_POST['health_leo'];
		$healthLibra = $_POST['health_libra'];
		$healthPisces = $_POST['health_pisces'];
		$healthSagitarius = $_POST['health_sagitarius'];
		$healthScorpio = $_POST['health_scorpio'];
		$healthTaurus = $_POST['health_taurus'];
		$healthVirgo = $_POST['health_virgo'];
		$artistMan1 = $_POST['artist_man_1'];
		$artistMan2 = $_POST['artist_man_2'];
		$artistMan3 = $_POST['artist_man_3'];
		$artistMan4 = $_POST['artist_man_4'];
		$artistMan5 = $_POST['artist_man_5'];
		$artistMan6 = $_POST['artist_man_6'];
		$artistMan7 = $_POST['artist_man_7'];
		$artistMan8 = $_POST['artist_man_8'];
		$artistWoman1 = $_POST['artist_woman_1'];
		$artistWoman2 = $_POST['artist_woman_2'];
		$artistWoman3 = $_POST['artist_woman_3'];
		$artistWoman4 = $_POST['artist_woman_4'];
		$artistWoman5 = $_POST['artist_woman_5'];
		$artistWoman6 = $_POST['artist_woman_6'];
		$artistWoman7 = $_POST['artist_woman_7'];
		$artistWoman8 = $_POST['artist_woman_8'];
		$this->db->where('type', 'common');
		$this->db->update('zodiacs', array(
			'description_en' => $common
		));
		$this->db->where('type', 'romance_aquarius');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceAquarius
		));
		$this->db->where('type', 'romance_aries');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceAries
		));
		$this->db->where('type', 'romance_cancer');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceCancer
		));
		$this->db->where('type', 'romance_capricorn');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceCapricorn
		));
		$this->db->where('type', 'romance_gemini');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceGemini
		));
		$this->db->where('type', 'romance_leo');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceLeo
		));
		$this->db->where('type', 'romance_libra');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceLibra
		));
		$this->db->where('type', 'romance_pisces');
		$this->db->update('zodiacs', array(
			'description_en' => $romancePisces
		));
		$this->db->where('type', 'romance_sagitarius');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceSagitarius
		));
		$this->db->where('type', 'romance_scorpio');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceScorpio
		));
		$this->db->where('type', 'romance_taurus');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceTaurus
		));
		$this->db->where('type', 'romance_virgo');
		$this->db->update('zodiacs', array(
			'description_en' => $romanceVirgo
		));
		
		
		$this->db->where('type', 'deficiency_aquarius');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyAquarius
		));
		$this->db->where('type', 'deficiency_aries');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyAries
		));
		$this->db->where('type', 'deficiency_cancer');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyCancer
		));
		$this->db->where('type', 'deficiency_capricorn');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyCapricorn
		));
		$this->db->where('type', 'deficiency_gemini');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyGemini
		));
		$this->db->where('type', 'deficiency_leo');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyLeo
		));
		$this->db->where('type', 'deficiency_libra');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyLibra
		));
		$this->db->where('type', 'deficiency_pisces');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyPisces
		));
		$this->db->where('type', 'deficiency_sagitarius');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencySagitarius
		));
		$this->db->where('type', 'deficiency_scorpio');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyScorpio
		));
		$this->db->where('type', 'deficiency_taurus');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyTaurus
		));
		$this->db->where('type', 'deficiency_virgo');
		$this->db->update('zodiacs', array(
			'description_en' => $deficiencyVirgo
		));
		
		
		$this->db->where('type', 'finance_aquarius');
		$this->db->update('zodiacs', array(
			'description_en' => $financeAquarius
		));
		$this->db->where('type', 'finance_aries');
		$this->db->update('zodiacs', array(
			'description_en' => $financeAries
		));
		$this->db->where('type', 'finance_cancer');
		$this->db->update('zodiacs', array(
			'description_en' => $financeCancer
		));
		$this->db->where('type', 'finance_capricorn');
		$this->db->update('zodiacs', array(
			'description_en' => $financeCapricorn
		));
		$this->db->where('type', 'finance_gemini');
		$this->db->update('zodiacs', array(
			'description_en' => $financeGemini
		));
		$this->db->where('type', 'finance_leo');
		$this->db->update('zodiacs', array(
			'description_en' => $financeLeo
		));
		$this->db->where('type', 'finance_libra');
		$this->db->update('zodiacs', array(
			'description_en' => $financeLibra
		));
		$this->db->where('type', 'finance_pisces');
		$this->db->update('zodiacs', array(
			'description_en' => $financePisces
		));
		$this->db->where('type', 'finance_sagitarius');
		$this->db->update('zodiacs', array(
			'description_en' => $financeSagitarius
		));
		$this->db->where('type', 'finance_scorpio');
		$this->db->update('zodiacs', array(
			'description_en' => $financeScorpio
		));
		$this->db->where('type', 'finance_taurus');
		$this->db->update('zodiacs', array(
			'description_en' => $financeTaurus
		));
		$this->db->where('type', 'finance_virgo');
		$this->db->update('zodiacs', array(
			'description_en' => $financeVirgo
		));
		
		$this->db->where('type', 'health_aquarius');
		$this->db->update('zodiacs', array(
			'description_en' => $healthAquarius
		));
		$this->db->where('type', 'health_aries');
		$this->db->update('zodiacs', array(
			'description_en' => $healthAries
		));
		$this->db->where('type', 'health_cancer');
		$this->db->update('zodiacs', array(
			'description_en' => $healthCancer
		));
		$this->db->where('type', 'health_capricorn');
		$this->db->update('zodiacs', array(
			'description_en' => $healthCapricorn
		));
		$this->db->where('type', 'health_gemini');
		$this->db->update('zodiacs', array(
			'description_en' => $healthGemini
		));
		$this->db->where('type', 'health_leo');
		$this->db->update('zodiacs', array(
			'description_en' => $healthLeo
		));
		$this->db->where('type', 'health_libra');
		$this->db->update('zodiacs', array(
			'description_en' => $healthLibra
		));
		$this->db->where('type', 'health_pisces');
		$this->db->update('zodiacs', array(
			'description_en' => $healthPisces
		));
		$this->db->where('type', 'health_sagitarius');
		$this->db->update('zodiacs', array(
			'description_en' => $healthSagitarius
		));
		$this->db->where('type', 'health_scorpio');
		$this->db->update('zodiacs', array(
			'description_en' => $healthScorpio
		));
		$this->db->where('type', 'health_taurus');
		$this->db->update('zodiacs', array(
			'description_en' => $healthTaurus
		));
		$this->db->where('type', 'health_virgo');
		$this->db->update('zodiacs', array(
			'description_en' => $healthVirgo
		));
		
		$this->db->where('type', 'artist_man_1');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan1
		));
		$this->db->where('type', 'artist_man_2');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan2
		));
		$this->db->where('type', 'artist_man_3');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan3
		));
		$this->db->where('type', 'artist_man_4');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan4
		));
		$this->db->where('type', 'artist_man_5');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan5
		));
		$this->db->where('type', 'artist_man_6');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan6
		));
		$this->db->where('type', 'artist_man_7');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan7
		));
		$this->db->where('type', 'artist_man_8');
		$this->db->update('zodiacs', array(
			'description_en' => $artistMan8
		));
		
		$this->db->where('type', 'artist_woman_1');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman1
		));
		$this->db->where('type', 'artist_woman_2');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman2
		));
		$this->db->where('type', 'artist_woman_3');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman3
		));
		$this->db->where('type', 'artist_woman_4');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman4
		));
		$this->db->where('type', 'artist_woman_5');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman5
		));
		$this->db->where('type', 'artist_woman_6');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman6
		));
		$this->db->where('type', 'artist_woman_7');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman7
		));
		$this->db->where('type', 'artist_woman_8');
		$this->db->update('zodiacs', array(
			'description_en' => $artistWoman8
		));
	}
}
