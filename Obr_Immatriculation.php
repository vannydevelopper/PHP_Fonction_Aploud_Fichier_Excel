<?php

/**
 *NTAHIMPERA Martin Luther King
 *	Element de la police 
 **/
class Obr_Immatriculation extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->have_droit();
	}

	public function have_droit()
	{
		if ($this->session->userdata('IMMATRICULATION') != 1 && $this->session->userdata('PSR_ELEMENT') != 1) {

			redirect(base_url());
		}
	}
	function index()
	{
		$data['title'] = 'Liste d\'Immatriculation';
		$this->load->view('obr/immatriculation_list', $data);
	}

	function listing()
	{

		$query_principal = 'SELECT ID_IMMATRICULATION, NUMERO_CARTE_ROSE, NUMERO_PLAQUE, CATEGORIE_PLAQUE, MARQUE_VOITURE, NUMERO_CHASSIS, NOMBRE_PLACE, NOM_PROPRIETAIRE, PRENOM_PROPRIETAIRE, NUMERO_IDENTITE, CATEGORIE_PROPRIETAIRE, CATEGORIE_USAGE, PUISSANCE, COULEUR, ANNEE_FABRICATION, DATE_INSERTION, MODELE_VOITURE, POIDS, TYPE_CARBURANT, TAXE_DMC, NIF, TELEPHONE, EMAIL, DATE_DELIVRANCE FROM obr_immatriculations_voitures obr WHERE 1 ';

		$var_search = !empty($_POST['search']['value']) ? $_POST['search']['value'] : null;

		$limit = 'LIMIT 0,10';


		if ($_POST['length'] != -1) {
			$limit = 'LIMIT ' . $_POST["start"] . ',' . $_POST["length"];
		}

		$order_by = '';


		$order_column = array('NUMERO_CARTE_ROSE', 'NUMERO_PLAQUE', 'CATEGORIE_PLAQUE', 'MARQUE_VOITURE', 'NUMERO_CHASSIS', 'NOMBRE_PLACE', 'NOM_PROPRIETAIRE', 'PRENOM_PROPRIETAIRE', 'NUMERO_IDENTITE', 'CATEGORIE_PROPRIETAIRE', 'CATEGORIE_USAGE', 'PUISSANCE', 'COULEUR', 'ANNEE_FABRICATION', 'DATE_INSERTION', 'MODELE_VOITURE', 'POIDS', 'TYPE_CARBURANT', 'TAXE_DMC', 'NIF', 'DATE_DELIVRANCE');

		$order_by = isset($_POST['order']) ? ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . '  ' . $_POST['order']['0']['dir'] : ' ORDER BY NOM,PRENOM ASC';

		$search = !empty($_POST['search']['value']) ? ("AND NUMERO_CARTE_ROSE LIKE '%$var_search%' OR NUMERO_PLAQUE LIKE '%$var_search%' OR NOM_PROPRIETAIRE LIKE '%$var_search%'  ") : '';

		$critaire = '';

		$query_secondaire = $query_principal . ' ' . $critaire . ' ' . $search . ' ' . $order_by . '   ' . $limit;
		$query_filter = $query_principal . ' ' . $critaire . ' ' . $search;

		$fetch_psr = $this->Modele->datatable($query_secondaire);
		$data = array();

		foreach ($fetch_psr as $row) {

			$option = '<div class="dropdown ">
			<a class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
			<i class="fa fa-cog"></i>
			Action
			<span class="caret"></span></a>
			<ul class="dropdown-menu dropdown-menu-left">
			';

			$option .= "<li><a hre='#' data-toggle='modal'
			data-target='#mydelete" . $row->ID_IMMATRICULATION . "'><font color='red'>&nbsp;&nbsp;Supprimer</font></a></li>";
			$option .= "<li><a class='btn-md' href='" . base_url('PSR/Obr_Immatriculation/getOne/' . $row->ID_IMMATRICULATION) . "'><label class='text-info'>&nbsp;&nbsp;Modifier</label></a></li>";
			$option .= "<li><a class='btn-md' href='" . base_url('PSR/Obr_Immatriculation/show_vehicule/' . $row->ID_IMMATRICULATION . '/' . $row->NUMERO_PLAQUE) . "'><label class='text-info'>&nbsp;&nbsp;Tableau de bord</label></a></li>";
			$option .= " </ul>
			</div>
			<div class='modal fade' id='mydelete" .  $row->ID_IMMATRICULATION . "'>
			<div class='modal-dialog'>
			<div class='modal-content'>

			<div class='modal-body'>
			<center><h5><strong>Voulez-vous supprimer?</strong> <br><b style='background-color:prink;color:green;'><i>" . $row->NOM_PROPRIETAIRE . "</i></b></h5></center>
			</div>

			<div class='modal-footer'>
			<a class='btn btn-danger btn-md' href='" . base_url('PSR/Obr_Immatriculation/delete/' . $row->ID_IMMATRICULATION) . "'>Supprimer</a>
			<button class='btn btn-primary btn-md' data-dismiss='modal'>Quitter</button>
			</div>

			</div>
			</div>
			</div>";

			$sub_array = array();

			$sub_array[] = $row->NUMERO_CARTE_ROSE;
			$sub_array[] = "<a  class='btn btn-md dt-button btn-sm' href='" . base_url('PSR/Obr_Immatriculation/show_vehicule/' . $row->ID_IMMATRICULATION . '/' . $row->NUMERO_PLAQUE) . "'>" . $row->NUMERO_PLAQUE . "</a>";
			$sub_array[] = $row->CATEGORIE_PLAQUE;
			$sub_array[] = $row->MARQUE_VOITURE;
			$sub_array[] = $row->NUMERO_CHASSIS;
			$sub_array[] = $row->NOMBRE_PLACE;
			$sub_array[] = $row->NOM_PROPRIETAIRE;
			$sub_array[] = $row->PRENOM_PROPRIETAIRE;
			$sub_array[] = $row->NUMERO_IDENTITE;
			$sub_array[] = $row->CATEGORIE_PROPRIETAIRE;
			$sub_array[] = $row->CATEGORIE_USAGE;
			$sub_array[] = $row->PUISSANCE;
			$sub_array[] = $row->COULEUR;
			$sub_array[] = $row->ANNEE_FABRICATION;
			$sub_array[] = $row->MODELE_VOITURE;
			$sub_array[] = $row->POIDS;
			$sub_array[] = $row->TYPE_CARBURANT;
			$sub_array[] = $row->TAXE_DMC;
			$sub_array[] = $row->NIF;
			$sub_array[] = $row->TELEPHONE . "<br>" . $row->EMAIL;
			$sub_array[] = $row->DATE_DELIVRANCE;
			$sub_array[] = $option;
			$data[] = $sub_array;
		}


		$output = array(
			"draw" => intval($_POST['draw']),
			"recordsTotal" => $this->Modele->all_data($query_principal),
			"recordsFiltered" => $this->Modele->filtrer($query_filter),
			"data" => $data
		);
		echo json_encode($output);
	}

	function ajouter()
	{
		$data['provinces'] = $this->Model->getRequete('SELECT PROVINCE_ID,PROVINCE_NAME FROM syst_provinces WHERE 1 ORDER BY PROVINCE_NAME ASC');
		$data['title'] = 'Nouveau immatriculation';
		$this->load->view('obr/immatriculation_add_v', $data);
	}

	function show_vehicule($ID_IMMATRICULATION, $NUMERO_PLAQUE)
	{

		$date = $this->input->post('date');
		$conditon = !empty($date) ? " AND DATE_FORMAT(h.DATE_INSERTION, '%d-%m-%Y')='" . $date . "'" : "";

		//print_r($conditon);die();
		$carte_rose = $this->Modele->getRequeteOne('SELECT ID_IMMATRICULATION, NUMERO_CARTE_ROSE, NUMERO_PLAQUE, CATEGORIE_PLAQUE, MARQUE_VOITURE, NUMERO_CHASSIS, NOMBRE_PLACE, NOM_PROPRIETAIRE, PRENOM_PROPRIETAIRE, NUMERO_IDENTITE, PROVINCE, CATEGORIE_PROPRIETAIRE, CATEGORIE_USAGE, PUISSANCE, COULEUR, ANNEE_FABRICATION, DATE_INSERTION, MODELE_VOITURE, POIDS, TYPE_CARBURANT, TAXE_DMC, NIF, DATE_DELIVRANCE FROM obr_immatriculations_voitures WHERE 1  AND ID_IMMATRICULATION =' . $ID_IMMATRICULATION);

		$vol = $this->Modele->getRequeteOne('SELECT ID_DECLARATION, NUMERO_PLAQUE, NOM_DECLARANT, PRENOM_DECLARANT, COULEUR_VOITURE, MARQUE_VOITURE, DATE_VOLER, STATUT FROM pj_declarations WHERE 1 AND NUMERO_PLAQUE=' . '"' . $NUMERO_PLAQUE . '"');
		$controle = $this->Modele->getRequeteOne('SELECT ID_CONTROLE, NUMERO_CONTROLE, NUMERO_PLAQUE, NUMERO_CHASSIS, PROPRIETAIRE, DATE_DEBUT, DATE_VALIDITE, TYPE_VEHICULE FROM otraco_controles WHERE 1 AND NUMERO_PLAQUE=' . '"' . $NUMERO_PLAQUE . '"');

		$assurance = $this->Modele->getRequeteOne('SELECT ID_ASSURANCE, NUMERO_PLAQUE, av.ID_ASSUREUR,assureur.ASSURANCE, DATE_DEBUT, DATE_VALIDITE, PLACES_ASSURES, TYPE_ASSURANCE, NOM_PROPRIETAIRE FROM assurances_vehicules av LEFT JOIN assureur ON assureur.ID_ASSUREUR=av.ID_ASSUREUR WHERE 1 AND NUMERO_PLAQUE=' . '"' . $NUMERO_PLAQUE . '"');
		//print_r($vol);die();

		$carte = $this->Modele->getRequete("SELECT SUM(`MONTANT`) AS AMANDE, DATE_FORMAT(h.DATE_INSERTION, '%d-%m-%Y') AS JOURS FROM historiques h JOIN utilisateurs u ON u.ID_UTILISATEUR=h.ID_UTILISATEUR  WHERE h.NUMERO_PLAQUE='" . $NUMERO_PLAQUE . "' " . $conditon . " GROUP by DATE_FORMAT(h.DATE_INSERTION, '%d-%m-%Y')");
		$day = $this->Modele->getRequete('SELECT date_format(DATE_INSERTION,"%d-%m-%Y") as date FROM historiques WHERE 1 GROUP BY date_format(DATE_INSERTION,"%d-%m-%Y")');



		$geolocalisation = $this->Modele->getRequete('SELECT LATITUDE, LONGITUDE,DATE_INSERTION FROM historiques WHERE 1 AND NUMERO_PLAQUE= "' . $NUMERO_PLAQUE . '"');

		$nombre = 0;

		$donne = "";
		$date = "";
		$montant = '';
		$donne_carte = '';

		foreach ($geolocalisation as $key => $value) {

			$lat = $value['LATITUDE'] != null ? $value['LATITUDE'] : 1;
			$long = $value['LONGITUDE'] != null ? $value['LONGITUDE'] : 1;

			//print_r($lat);die();
			$donne_carte .= 'var fixedMarker = L.marker(new L.LatLng(' . $lat . ',' . $long . '), {
				icon: L.mapbox.marker.icon({
					"marker-color": "ff8888"
					})
				}).bindPopup("<center><b>' . $carte_rose['NOM_PROPRIETAIRE'] . ' ' . $carte_rose['PRENOM_PROPRIETAIRE'] . '</b><br>' . $carte_rose['NUMERO_PLAQUE'] . '<br>' . $carte_rose['DATE_INSERTION'] . '</center>").addTo(map);';
		}



		if (!empty($carte)) {

			foreach ($carte as  $value) {
				// print_r($carte);die();
				$name = (!empty($value['JOURS'])) ? $value['JOURS'] : '';
				$nb = (!empty($value['AMANDE'])) ? $value['AMANDE'] : 0;
				// $date.= "'".$value['JOURS']."',";
				// $montant =  $value['AMANDE'].",";
				$montant .= "{name:'" . str_replace("'", "\'", $name) . "',y:" . $nb . "},";
				$nombre += $value['AMANDE'];
				//print_r($montant);die();
			}
		} else {
			$x = 0;
			$date .= "'" . date('d-m-Y') . "',";
			$montant .=  $x . ",";
			$nombre += 0;
		}


		$date .= "@";
		$date = str_replace(",@", "", $date);

		$montant .= "@";
		$montant = str_replace(",@", "", $montant);



		$data['donne_carte'] = $donne_carte;
		$data['amandes'] = $montant;
		$data['date'] = $date;
		$data['total'] = $nombre;

		$data['carte_rose'] = $carte_rose;
		$data['vol'] = $vol;
		$data['controle'] = $controle;
		$data['assurance'] = $assurance;
		$data['day'] = $day;

		$data['ID_IMMATRICULATION'] = $ID_IMMATRICULATION;
		$data['NUMERO_PLAQUE'] = $NUMERO_PLAQUE;
		$data['title'] = 'Detail sur le Véhicule';
		$this->load->view('espace_perso/profil_vehicule_list_v', $data);
	}




	function add()
	{

		$this->form_validation->set_rules('NUMERO_CARTE_ROSE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_PLAQUE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('CATEGORIE_PLAQUE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('MARQUE_VOITURE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_CHASSIS', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NOMBRE_PLACE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NOM_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('PRENOM_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_IDENTITE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		// $this->form_validation->set_rules('ID_PROVINCE','', 'trim|required',array('required'=>'<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('CATEGORIE_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('CATEGORIE_USAGE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('PUISSANCE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('COULEUR', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('ANNEE_FABRICATION', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('MODELE_VOITURE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('POIDS', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('TYPE_CARBURANT', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('TAXE_DMC', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('NIF', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('DATE_DELIVRANCE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		if ($this->form_validation->run() == FALSE) {
			$this->ajouter();
		} else {
			$data_insert = array(
				'NUMERO_CARTE_ROSE' => $this->input->post('NUMERO_CARTE_ROSE'),
				'NUMERO_PLAQUE' => $this->input->post('NUMERO_PLAQUE'),
				'CATEGORIE_PLAQUE' => $this->input->post('CATEGORIE_PLAQUE'),
				'MARQUE_VOITURE' => $this->input->post('MARQUE_VOITURE'),
				'NUMERO_CHASSIS' => $this->input->post('NUMERO_CHASSIS'),
				'NOMBRE_PLACE' => $this->input->post('NOMBRE_PLACE'),
				'NOM_PROPRIETAIRE' => $this->input->post('NOM_PROPRIETAIRE'),
				'PRENOM_PROPRIETAIRE' => $this->input->post('PRENOM_PROPRIETAIRE'),
				'TELEPHONE' => $this->input->post('TELEPHONE'),
				'NUMERO_IDENTITE' => $this->input->post('NUMERO_IDENTITE'),
				// 'PROVINCE_ID'=>$this->input->post('ID_PROVINCE'),
				'CATEGORIE_PROPRIETAIRE' => $this->input->post('CATEGORIE_PROPRIETAIRE'),
				'CATEGORIE_USAGE' => $this->input->post('CATEGORIE_USAGE'),
				'PUISSANCE' => $this->input->post('PUISSANCE'),
				'COULEUR' => $this->input->post('COULEUR'),
				'ANNEE_FABRICATION' => $this->input->post('ANNEE_FABRICATION'),
				'MODELE_VOITURE' => $this->input->post('MODELE_VOITURE'),
				'POIDS' => $this->input->post('POIDS'),
				'TYPE_CARBURANT' => $this->input->post('TYPE_CARBURANT'),
				'TAXE_DMC' => $this->input->post('TAXE_DMC'),
				'NIF' => $this->input->post('NIF'),
				'DATE_DELIVRANCE' => $this->input->post('DATE_DELIVRANCE'),
				'EMAIL' => $this->input->post('EMAIL'),

			);

			$tabl = 'obr_immatriculations_voitures';
			$this->Modele->create($tabl, $data_insert);

			$data['message'] = '<div class="alert alert-success text-center" id="message">' . "L'ajout se faite avec succès" . '</div>';
			$this->session->set_flashdata($data);
			redirect(base_url('PSR/Obr_Immatriculation/'));
		}
	}
	function getOne($id = 0)
	{

		$data['membre'] = $this->Modele->getRequeteOne('SELECT * FROM obr_immatriculations_voitures  WHERE ID_IMMATRICULATION=' . $id);
		$data['provinces'] = $this->Model->getRequete('SELECT PROVINCE_ID,PROVINCE_NAME FROM syst_provinces WHERE 1 ORDER BY PROVINCE_NAME ASC');

		$data['title'] = "Modification d'un Immatriculation";
		$this->load->view('obr/immatriculation_update_v', $data);
	}

	function update()
	{
		$this->form_validation->set_rules('NUMERO_CARTE_ROSE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_PLAQUE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('CATEGORIE_PLAQUE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('MARQUE_VOITURE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_CHASSIS', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NOMBRE_PLACE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NOM_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('PRENOM_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_IDENTITE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('ID_PROVINCE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('CATEGORIE_PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('CATEGORIE_USAGE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('PUISSANCE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('COULEUR', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('ANNEE_FABRICATION', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('MODELE_VOITURE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('POIDS', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('TYPE_CARBURANT', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('TAXE_DMC', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));


		$this->form_validation->set_rules('NIF', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('DATE_DELIVRANCE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));



		$id = $this->input->post('ID_IMMATRICULATION');

		if ($this->form_validation->run() == FALSE) {
			//$id=$this->input->post('ID_GERANT');

			$this->getOne();
		} else {
			$id = $this->input->post('ID_IMMATRICULATION');

			$data = array(
				'NUMERO_CARTE_ROSE' => $this->input->post('NUMERO_CARTE_ROSE'),
				'NUMERO_PLAQUE' => $this->input->post('NUMERO_PLAQUE'),
				'CATEGORIE_PLAQUE' => $this->input->post('CATEGORIE_PLAQUE'),
				'MARQUE_VOITURE' => $this->input->post('MARQUE_VOITURE'),
				'NUMERO_CHASSIS' => $this->input->post('NUMERO_CHASSIS'),
				'NOMBRE_PLACE' => $this->input->post('NOMBRE_PLACE'),
				'NOM_PROPRIETAIRE' => $this->input->post('NOM_PROPRIETAIRE'),
				'PRENOM_PROPRIETAIRE' => $this->input->post('PRENOM_PROPRIETAIRE'),
				'TELEPHONE' => $this->input->post('TELEPHONE'),
				'NUMERO_IDENTITE' => $this->input->post('NUMERO_IDENTITE'),
				'CATEGORIE_PROPRIETAIRE' => $this->input->post('CATEGORIE_PROPRIETAIRE'),
				'CATEGORIE_USAGE' => $this->input->post('CATEGORIE_USAGE'),
				'PUISSANCE' => $this->input->post('PUISSANCE'),
				'COULEUR' => $this->input->post('COULEUR'),
				'ANNEE_FABRICATION' => $this->input->post('ANNEE_FABRICATION'),
				'MODELE_VOITURE' => $this->input->post('MODELE_VOITURE'),
				'POIDS' => $this->input->post('POIDS'),
				'TYPE_CARBURANT' => $this->input->post('TYPE_CARBURANT'),
				'TAXE_DMC' => $this->input->post('TAXE_DMC'),
				'NIF' => $this->input->post('NIF'),
				'DATE_DELIVRANCE' => $this->input->post('DATE_DELIVRANCE'),
				'EMAIL' => $this->input->post('EMAIL'),
			);

			$this->Modele->update('obr_immatriculations_voitures', array('ID_IMMATRICULATION' => $id), $data);
			$datas['message'] = '<div class="alert alert-success text-center" id="message">La modification du menu est faite avec succès</div>';
			$this->session->set_flashdata($datas);
			redirect(base_url('PSR/Obr_Immatriculation/'));
		}
	}

	function delete()
	{
		$table = "obr_immatriculations_voitures";
		$criteres['ID_IMMATRICULATION'] = $this->uri->segment(4);
		$data['rows'] = $this->Modele->getOne($table, $criteres);
		$this->Modele->delete($table, $criteres);

		$data['message'] = '<div class="alert alert-success text-center" id="message">L"Element est supprimé avec succès</div>';
		$this->session->set_flashdata($data);
		redirect(base_url('PSR/Obr_Immatriculation'));
	}

	public function add_excel()
	{



		$highestRow=0;
        $path = $_FILES["FICHIER"]["tmp_name"];
        $object = PHPExcel_IOFactory::load($path);

      foreach($object->getWorksheetIterator() as $worksheet)
      {
        $highestRow.=$worksheet->getHighestRow();
        $highestColumn=$worksheet->getHighestColumn();
    //print_r($highestRow);die();
        $i=0;
        for($row=2; $row<=$highestRow; $row++)
        {
            //     

          $NUMERO_CARTE_ROSE=$worksheet->getCellByColumnAndRow(0, $row)->getValue();
          $NUMERO_PLAQUE=$worksheet->getCellByColumnAndRow(1, $row)->getValue();
          $CATEGORIE_PLAQUE=$worksheet->getCellByColumnAndRow(2, $row)->getValue();
          $MARQUE_VOITURE=$worksheet->getCellByColumnAndRow(3, $row)->getValue();
          $NUMERO_CHASSIS=$worksheet->getCellByColumnAndRow(4, $row)->getValue();
          $NOMBRE_PLACE=$worksheet->getCellByColumnAndRow(5, $row)->getValue();
          $NOM_PROPRIETAIRE=$worksheet->getCellByColumnAndRow(6, $row)->getValue();
          $PRENOM_PROPRIETAIRE =$worksheet->getCellByColumnAndRow(7, $row)->getValue();
          $NUMERO_IDENTITE=$worksheet->getCellByColumnAndRow(8, $row)->getValue();
          $TELEPHONE=$worksheet->getCellByColumnAndRow(9, $row)->getValue();
          $PROVINCE=$worksheet->getCellByColumnAndRow(10, $row)->getValue();
          $CATEGORIE_PROPRIETAIRE=$worksheet->getCellByColumnAndRow(11, $row)->getValue();
          $CATEGORIE_USAGE=$worksheet->getCellByColumnAndRow(12, $row)->getValue();
          $PUISSANCE=$worksheet->getCellByColumnAndRow(13, $row)->getValue();
          $COULEUR=$worksheet->getCellByColumnAndRow(14, $row)->getValue();
          $ANNEE_FABRICATION=$worksheet->getCellByColumnAndRow(15, $row)->getValue();
          $MODELE_VOITURE=$worksheet->getCellByColumnAndRow(16, $row)->getValue();
          $POIDS=$worksheet->getCellByColumnAndRow(17, $row)->getValue();
          $TYPE_CARBURANT=$worksheet->getCellByColumnAndRow(18, $row)->getValue();
          $TAXE_DMC=$worksheet->getCellByColumnAndRow(19, $row)->getValue();
          $NIF=$worksheet->getCellByColumnAndRow(20, $row)->getValue();
          $DATE_DELIVRANCE=$worksheet->getCellByColumnAndRow(21, $row)->getValue();
          $EMAIL=$worksheet->getCellByColumnAndRow(22, $row)->getValue();
				
          $data_insert = array(
				'NUMERO_CARTE_ROSE' => trim($NUMERO_CARTE_ROSE),
				'NUMERO_PLAQUE' => trim($NUMERO_PLAQUE),
				'CATEGORIE_PLAQUE' => trim($CATEGORIE_PLAQUE),
				'MARQUE_VOITURE' => trim($MARQUE_VOITURE),
				'NUMERO_CHASSIS' => trim($NUMERO_CHASSIS),
				'NOMBRE_PLACE' => trim($NOMBRE_PLACE),
				'NOM_PROPRIETAIRE' => trim($NOM_PROPRIETAIRE),
				'PRENOM_PROPRIETAIRE' => trim($PRENOM_PROPRIETAIRE),
				'NUMERO_IDENTITE' => trim($NUMERO_IDENTITE),
				'TELEPHONE' => trim($TELEPHONE),
				'PROVINCE' => trim($PROVINCE),
				'CATEGORIE_PROPRIETAIRE' => trim($CATEGORIE_PROPRIETAIRE),
				'CATEGORIE_USAGE' => trim($CATEGORIE_USAGE),
				'PUISSANCE' => trim($PUISSANCE),
				'COULEUR' => trim($COULEUR),
				'ANNEE_FABRICATION' => trim($ANNEE_FABRICATION),
				'MODELE_VOITURE' => trim($MODELE_VOITURE),
				'POIDS' => trim($POIDS),
				'TYPE_CARBURANT' => trim($TYPE_CARBURANT),
				'TAXE_DMC' => trim($TAXE_DMC),
				'NIF' => trim($NIF),
				'DATE_DELIVRANCE' => trim($DATE_DELIVRANCE),
				'EMAIL' => trim($EMAIL),
			);

        // echo "<pre>";
        // print_r($data_importation);
        // echo "</pre>";

			$tabl = 'obr_immatriculations_voitures';
			$this->Modele->create($tabl, $data_insert);

        }

    }  /* 
         */

    $donnee['message']='<div id="message" class="alert alert-info text-center">Importé avec succès</div>';
    $this->session->set_flashdata($donnee);
    redirect(base_url('PSR/Obr_Immatriculation/index'));

		

	}
}
