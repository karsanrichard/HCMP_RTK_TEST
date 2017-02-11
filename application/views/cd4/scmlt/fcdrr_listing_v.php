
<script type="text/javascript">

$(document).ready(function() {

    $('#example').dataTable({
        "sDom": "T lfrtip",
            "aaSorting": [[4, 'desc']],
            "bPaginate": true,            
            "sScrollY": "377px",
            "sScrollX": "100%",
            // "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ Records per page",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ records",
              }
    });

    // $("table").tablecloth({theme: "paper"});   

});

</script>

<style>

    .dash_main{
        width: 94%;       
        height:520px;   
        margin-left:90px;
        margin-bottom:0em;
    }
  
</style>

<?php include ('scmlt_sidebar.php');?>
      
<div class="dash_main" id="dash_main">        
<div id="tablediv" style="margin-left:190px;">
    <div id="notification" style="margin-left:280px;" >
        <h3> <b>CD4 orders for <?php echo $d_name; ?> Sub-County Below </b></h3>
        <br/>
        <br/>
    </div>  
<?php if (count($lab_order_list) > 0) : ?>
<table width="100%" id="example" class="table table-bordered table-stripped">
  <thead>
    <tr>
      <th>Reports for</th>
      <th>MFLCode</th>
      <th>Facility Name</th>
      <th>Compiled By</th>
      <th>Order Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($lab_order_list as $order) {

      $english_date = date('D dS M Y',strtotime($order['order_date']));
      $reportmonth = date('F Y',strtotime('-1 month',strtotime($order['order_date'])));

      ?>
      <tr>
        <td><?php echo $reportmonth; ?></td>        
        <td><?php echo $order['facility_code']; ?></td>
        <td><?php echo $order['facility_name']; ?></td>
        <td><?php if ($order['compiled_by']=='' || $order['compiled_by'] ==0) { echo "N/A";} else{echo $order['compiled_by']; }?></td>
        <td><?php echo $english_date; ?></td>
        <td><a href="<?php echo site_url('cd4_management/fcdrr_details/' . $order['id']); ?>"class="link">View</a> </td>
      </tr> 
    <?php
    }
    ?>
  </tbody>
</table>
<?php
else :
  echo '<p id="notification">No Records Found</p>';
endif;
?>
</div>
</div>

