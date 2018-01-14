<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <2016>  <jamelbaz@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


// Load Dolibarr environment
if (false === (@include '../main.inc.php')) {  // From htdocs directory
	require '../../main.inc.php'; // From "custom" directory
}
require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/invoice.lib.php';
$action=GETPOST('action','alpha');

$langs->load("companies");

// Security check
$id = GETPOST('id','int');
$soc_id = GETPOST('soc_id','int');
$montant = GETPOST('montant');
if ($user->societe_id) $id=$user->societe_id;
$result = restrictedArea($user, 'societe', $id, '&societe');


$object = new Facture($db);


if($id) $ret = $object->fetch($id);
$object->fetch_lines();
$lines = $object->lines;
// print_r($lines);
//var_dump($object->lines);
$societe = new Societe($db);


llxHeader('', 'Logistique', 'EN:Customers_Invoices|FR:Factures_Clients|ES:Facturas_a_clientes');

if ($id > 0)
{
    /*
     * Affichage onglets
     */
    if (! empty($conf->notification->enabled)) $langs->load("mails");

    $head = facture_prepare_head($object);

    dol_fiche_head($head, 'tabname1', $langs->trans('InvoiceCustomer'), 0, 'Logistique');
    
    $linkback = '<a href="'.DOL_URL_ROOT.'/compta/facture/list/list.php">'.$langs->trans("BackToList").'</a>';

        dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');


        print '<div class="fichecenter">';

        print '<div class="underbanner clearboth"></div>';
        print '<table class="border tableforfield" width="100%">';
    
    
    print "</table>";

        print '</div>';
        print '<div style="clear:both"></div>';

		dol_fiche_end();
    
    
    
    
    
    
   // print_r($data)
 ?> 

<div class="fichecenter">
	  

			
<table class="noborder" width="100%">
	<caption>Repartitions</caption>
	<tbody>
	   <tr class="liste_titre">
		  <td>Produits</td>
		  <td>Poid</td>
		  <td>Volume</td>
		  <td>QTE</td>
		  <td>Total Poids</td>
		  <td>Total Volume</td>
		  <td>QTE Par Pal.</td>
		  <td>Nbr Pal.</td>
		  <td>Qte Par Cam.</td>
		  <td>% Camion</td>
	   </tr>
	   <?php $somme = 0; 
	   foreach($lines as $k => $v): 
			$prodstatic = new Product($db);
            $prodstatic->fetch(null, $v->ref);
	   // var_dump($prodstatic->array_options);die;
	   // var_dump($v);die;
	   
	   ?>
	   <tr class="impair">
		  <td><?php echo $prodstatic->getNomUrl(1). ' '  .$v->libelle. ' '. $v->desc;?></td>
		  <td><?php echo $prodstatic->weight;?></td>
		  <td><?php echo $prodstatic->volume;?></td>
		  <td><?php echo $v->qty;?></td>
		  <td><?php $PoidsTotal = $v->qty * $prodstatic->weight; echo $PoidsTotal ;?></td>
		  <td><?php echo $v->qty * $prodstatic->volume;?></td>
		  <td><?php $qty_pal = $prodstatic->array_options['options_qtepal']; echo $qty_pal;?></td>
		  <td><?php $nb_pal = round($qty_pal/$v->qty,2); echo $nb_pal;?></td>
		  <td><?php $qtecam = $prodstatic->array_options['options_qtecam']; echo $qtecam;?></td>
		  <td><?php $cam = round($qtecam/$v->qty,2); echo $cam ;?></td>
	   </tr>
	   <?php endforeach;?>
	</tbody>
	<tfoot>
	
	
		<tr class="liste_titre">
		  <td align="center">Total:</td>
		   <td></td>
		  <td></td>
		  <td></td>
		  <td><?php $PoidsTotal ; ?></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
	   </tr>
	</tfoot>
</table>
  <div class="fichehalfright">
      </div>
   <div style="clear:both"></div>
</div>
<?php

    dol_fiche_end();
}

llxFooter();
$db->close();