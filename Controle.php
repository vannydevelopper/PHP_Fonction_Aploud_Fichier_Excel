<?php

/**
 *NTAHIMPERA Martin Luther King
 *	Element de la police 
 **/
class Controle extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->have_droit();
	}

	public function have_droit()
	{
		if ($this->session->userdata('CONTROLE_TECHNIQUE') != 1 && $this->session->userdata('PSR_ELEMENT') != 1) {

			redirect(base_url());
		}
	}
	function index()
	{
		$data['title'] = 'Element de Controle';
		$this->load->view('controle/controle_list', $data);
	}

	function listing()
	{
		$i = 1;
		$query_principal = 'SELECT ID_CONTROLE, NUMERO_CONTROLE, otraco.NUMERO_PLAQUE,obr.ID_IMMATRICULATION, otraco.NUMERO_CHASSIS, PROPRIETAIRE, DATE_DEBUT, DATE_VALIDITE,TYPE_VEHICULE FROM otraco_controles otraco LEFT JOIN obr_immatriculations_voitures obr ON obr.NUMERO_PLAQUE=otraco.NUMERO_PLAQUE WHERE 1';

		$var_search = !empty($_POST['search']['value']) ? $_POST['search']['value'] : null;

		$limit = 'LIMIT 0,10';


		if ($_POST['length'] != -1) {
			$limit = 'LIMIT ' . $_POST["start"] . ',' . $_POST["length"];
		}

		$order_by = '';


		$order_column = array('TYPE_VEHICULE', 'NUMERO_CONTROLE', 'NUMERO_PLAQUE', 'NUMERO_CHASSIS', 'PROPRIETAIRE', 'DATE_DEBUT', 'DATE_VALIDITE', 'TYPE_VEHICULE');

		$order_by = isset($_POST['order']) ? ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . '  ' . $_POST['order']['0']['dir'] : ' ORDER BY otraco.NUMERO_PLAQUE ASC';

		$search = !empty($_POST['search']['value']) ? ("AND otraco.NUMERO_PLAQUE LIKE '%$var_search%' OR NUMERO_CONTROLE LIKE '%$var_search%' ") : '';

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
			data-target='#mydelete" . $row->ID_CONTROLE . "'><font color='red'>&nbsp;&nbsp;Supprimer</font></a></li>";
			$option .= "<li><a class='btn-md' href='" . base_url('PSR/Controle/getOne/' . $row->ID_CONTROLE) . "'><label class='text-info'>&nbsp;&nbsp;Modifier</label></a></li>";
			$option .= " </ul>
			</div>
			<div class='modal fade' id='mydelete" .  $row->ID_CONTROLE . "'>
			<div class='modal-dialog'>
			<div class='modal-content'>

			<div class='modal-body'>
			<center><h5><strong>Voulez-vous supprimer?</strong> <br><b style='background-color:prink;color:green;'><i>" . $row->NUMERO_CONTROLE . "</i></b></h5></center>
			</div>

			<div class='modal-footer'>
			<a class='btn btn-danger btn-md' href='" . base_url('PSR/Controle/delete/' . $row->ID_CONTROLE) . "'>Supprimer</a>
			<button class='btn btn-primary btn-md' data-dismiss='modal'>Quitter</button>
			</div>

			</div>
			</div>
			</div>";

			$debut = date("d-m-Y", strtotime($row->DATE_DEBUT));
			$fin = date("d-m-Y", strtotime($row->DATE_VALIDITE));

			$sub_array = array();
			//$sub_array[]=$i++;
			$sub_array[] = $row->NUMERO_CONTROLE;
			if ($row->ID_IMMATRICULATION != null) {
				$sub_array[] = "<a  class='btn btn-md dt-button btn-sm' href='" . base_url('PSR/Obr_Immatriculation/show_vehicule/' . $row->ID_IMMATRICULATION . '/' . $row->NUMERO_PLAQUE) . "'>" . $row->NUMERO_PLAQUE . "</a>";
			} else {
				$sub_array[] = "<span style='color :red'>" . $row->NUMERO_PLAQUE . "</span>";
			}
			$sub_array[] = $row->NUMERO_CHASSIS;
			$sub_array[] = $row->PROPRIETAIRE;
			$sub_array[] = $debut;
			$sub_array[] = $fin;
			$sub_array[] = $this->notifications->ago($debut, $fin);
			$sub_array[] = $row->TYPE_VEHICULE;
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
		$data['plaques'] = $this->Model->getRequete('SELECT ID_IMMATRICULATION, NUMERO_PLAQUE FROM obr_immatriculations_voitures WHERE 1 ORDER BY NUMERO_PLAQUE ASC');

		$data['title'] = 'Nouveau Element';
		$this->load->view('controle/controle_add_v', $data);
	}



	function add()
	{

		$this->form_validation->set_rules('NUMERO_CONTROLE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_PLAQUE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('NUMERO_CHASSIS', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('PROPRIETAIRE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('DATE_DEBUT', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));
		$this->form_validation->set_rules('DATE_VALIDITE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));

		$this->form_validation->set_rules('TYPE_VEHICULE', '', 'trim|required', array('required' => '<font style="color:red;size:2px;">Le champ est Obligatoire</font>'));
		if ($this->form_validation->run() == FALSE) {
			$this->ajouter();
		} else {

			$data_insert = array(
				'NUMERO_CONTROLE' => $this->input->post('NUMERO_CONTROLE'),
				'NUMERO_PLAQUE' => $this->input->post('NUMERO_PLAQUE'),
				'NUMERO_CHASSIS' => $this->input->post('NUMERO_CHASSIS'),
				'PROPRIETAIRE' => $this->input->post('PROPRIETAIRE'),
				'DATE_DEBUT' => $this->input->post('DATE_DEBUT'),
				'DATE_VALIDITE' => $this->input->post('DATE_VALIDITE'),
				'TYPE_VEHICULE' => $this->input->post('TYPE_VEHICULE'),
			);
			$table = 'otraco_controles';

			$this->Modele->create($table, $data_insert);

			$data['message'] = '<div class="alert alert-success text-center" id="message">' . "L'ajout se faite avec succès" . '</div>';
			$this->session->set_flashdata($data);
			redirect(base_url('PSR/Controle/'));
		}
	}
	function getOne($id = 0)
	{
		$data['controles'] = $this->Modele->getOne('otraco_controles', array('ID_CONTROLE' => $id));
		$data['plaques'] = $this->Model->getRequete('SELECT ID_IMMATRICULATION, NUMERO_PLAQUE FROM obr_immatriculations_voitures WHERE 1 ORDER BY NUMERO_PLAQUE ASC');

		$data['title'] = "Modification d'un  controle technique";
		$this->load->view('controle/controle_update_v', $data);
	}

	function update()
	{

		$id = $this->input->post('ID_CONTROLE');

		$data_insert = array(
			'NUMERO_CONTROLE' => $this->input->post('NUMERO_CONTROLE'),
			'NUMERO_PLAQUE' => $this->input->post('NUMERO_PLAQUE'),
			'NUMERO_CHASSIS' => $this->input->post('NUMERO_CHASSIS'),
			'PROPRIETAIRE' => $this->input->post('PROPRIETAIRE'),
			'DATE_DEBUT' => $this->input->post('DATE_DEBUT'),
			'DATE_VALIDITE' => $this->input->post('DATE_VALIDITE'),
			'TYPE_VEHICULE' => $this->input->post('TYPE_VEHICULE'),
		);

		$this->Modele->update('otraco_controles', array('ID_CONTROLE' => $id), $data_insert);
		$datas['message'] = '<div class="alert alert-success text-center" id="message">La modification du menu est faite avec succès</div>';
		$this->session->set_flashdata($datas);
		redirect(base_url('PSR/Controle/'));
	}

	function delete()
	{
		$table = "otraco_controles";
		$criteres['ID_CONTROLE'] = $this->uri->segment(4);
		$data['rows'] = $this->Modele->getOne($table, $criteres);
		$this->Modele->delete($table, $criteres);

		$data['message'] = '<div class="alert alert-success text-center" id="message">L"Element est supprimé avec succès</div>';
		$this->session->set_flashdata($data);
		redirect(base_url('PSR/Controle'));
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
          $NUMERO_CONTROLE=$worksheet->getCellByColumnAndRow(0, $row)->getValue();
          $NUMERO_PLAQUE=$worksheet->getCellByColumnAndRow(1, $row)->getValue();
          $NUMERO_CHASSIS=$worksheet->getCellByColumnAndRow(2, $row)->getValue();
          $PROPRIETAIRE=$worksheet->getCellByColumnAndRow(3, $row)->getValue();

          $DATE_DEBUT=$worksheet->getCellByColumnAndRow(4, $row)->getValue();
          $DATE_DEBUT = PHPExcel_Style_NumberFormat::toFormattedString($DATE_DEBUT, 'YYYY-MM-DD');

          $DATE_VALIDITE=$worksheet->getCellByColumnAndRow(5, $row)->getValue();
          $DATE_VALIDITE = PHPExcel_Style_NumberFormat::toFormattedString($DATE_VALIDITE, 'YYYY-MM-DD');
          
          $TYPE_VEHICULE=$worksheet->getCellByColumnAndRow(6, $row)->getValue();
          
				
          $data_insert = array(
				'NUMERO_CONTROLE' => trim($NUMERO_CONTROLE),
				'NUMERO_PLAQUE' => trim($NUMERO_PLAQUE),
				'NUMERO_CHASSIS' => trim($NUMERO_CHASSIS),
				'PROPRIETAIRE' => trim($PROPRIETAIRE),
				'DATE_DEBUT' => trim($DATE_DEBUT),
				'DATE_VALIDITE' => trim($DATE_VALIDITE),
				'TYPE_VEHICULE' => trim($TYPE_VEHICULE),
			);

        // echo "<pre>";
        // print_r($data_importation);
        // echo "</pre>";

			$tabl = 'otraco_controles';
			$this->Modele->create($tabl, $data_insert);

        }

    }  /* 
         */

    $donnee['message']='<div id="message" class="alert alert-info text-center">Importé avec succès</div>';
    $this->session->set_flashdata($donnee);
    redirect(base_url('PSR/Controle/index'));

		

	}

}
