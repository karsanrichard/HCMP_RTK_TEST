<!-- Button to trigger modal -->
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablesorter.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.metadata.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url(); ?>assets/tablecloth/assets/js/jquery.tablecloth.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/tablecloth/assets/css/tablecloth.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>assets/datatable/jquery.dataTables.js"></script>



<button type="button" class="btn btn-default" data-toggle="modal" data-target="#Add_DMLT">Add SCMLT</button>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#Add_Facility">Add Facility Lab Tec</button>
<br/>
<div class="modal fade" id="Add_DMLT" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add SCMLT</h4>
      </div>
      <div class="modal-body">        
        <p></p>
        <form id="add_dmlt_form"> 
            <table>
                <tr>    
                    <td>First Name</td>
                    <td><input class="form-control" id="first_name" type="text" name="first_name" /></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input class="form-control" id="last_name" type="text" name="last_name" /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input class="form-control" id="email" type="text" name="email" /></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><input class="form-control" id="phone" type="text" name="phone" />
                        <input class="form-control" id="county" type="hidden" name="county" value="<?php echo $countyid; ?>" /></td>
                </tr>
                <tr>
                    <td>Sub-County</td>
                    <td>
                        <select id="district" class="form-control">
                            <option> -- Select Sub County --</option>
                            <?php foreach ($districts as $dists) { ?>
                                <option value="<?php echo $dists['id']; ?>"><?php echo$dists['district']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        </form>      

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="save_dmlt" class="btn btn-primary">Save Changes</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="Add_Facility" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Facility Lab Tech</h4>
      </div>
      <div class="modal-body">        
        <p></p>
        <form id="add_facility_form"> 
            <table>
                <tr>    
                    <td>First Name</td>
                    <td><input class="form-control" id="f_first_name" type="text" name="f_first_name" /></td>
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td><input class="form-control" id="f_last_name" type="text" name="f_last_name" /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input class="form-control" id="f_email" type="text" name="f_email" /></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td><input class="form-control" id="f_phone" type="text" name="f_phone" />
                        <input class="form-control" id="f_county" type="hidden" name="f_county" value="<?php echo $countyid; ?>" /></td>
                </tr>
                <tr>
                    <td>Sub County</td>
                    <td>
                        <select id="f_district" class="form-control">
                            <option value="0"> -- Select Sub County--</option>
                            <?php foreach ($districts as $dists) { ?>
                                <option value="<?php echo $dists['id']; ?>"><?php echo$dists['district']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Facilities</td>
                    <td>
                        <select id="f_facilities" class="form-control">
                            <option> -- Select Facilities--</option>
                          <?php foreach ($facilities as $facility) { ?>
                                <option value="<?php echo $facility['facility_code']; ?>"><?php echo$facility['facility_name']; ?></option>
                            <?php } ?> 
                        </select>
                    </td>
                </tr>
            </table>
        </form>

       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="save_facility" class="btn btn-primary">Save Changes</button>
      </div>
    </div>
  </div>
</div>




<table id="users_table" class="data-table">
    <thead>
   <!--  <th style="width: 10px;">Delete</th>  -->   
    <th>Name</th>
    <th>email</th>
    <th>Phone</th>
    <th>Main Sub County</th>
    <th>Other Sub Counties</th>

<!--    <th>Action</th>-->
</thead>
<tbody>
    <?php foreach ($users as $row) {?>
        <tr>            
            <!-- <td><a href="<?php echo '../delete_user/'.$row['id'].'/'.$row['district_id'].'/county_user'; ?>" title="Delete <?php echo $row['fname'] . ' ' . $row['lname']; ?>"><span style="color: #DD6A6A;">[x]</span></a></td> -->
            <td><a href="#user_<?php echo $row['id']; ?>"><?php echo $row['fname'] . ' ' . $row['lname']; ?></a></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['telephone']; ?></td>
            <td><?php echo $row['district']; ?></td>
            <td>
              <a href="#" type="button" class="dmlt_district" data-toggle="modal" data-target="#dmlt_district" id="<?php echo $row['id']?>" value="<?php echo $row['id']?>">Add  Sub-County</a>                
                <div id="districts_dmlt_<?php echo $row['id']; ?>"> </div>
                <script type="text/javascript">
                $(function(){
                  $( "#districts_dmlt_<?php echo $row['id']; ?>" ).load( "<?php echo base_url();?>rtk_management/show_dmlt_districts/<?php echo $row['id']; ?>" );
                });
                </script>
                
            </td>
        </tr>
    <?php } ?>
</tbody>
</table>

<div class="modal fade" id="dmlt_district" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Sub-County</h4>
                  </div>
                  <div class="modal-body">
                        <h5 id="dmlt_sub_label"></h5>
                        <hr />
                        <form id="add_dmlt_district_form" method="POST" action="<?php echo base_url() . 'rtk_management/dmlt_district_action'; ?>">
                            
                            <input class="form-control" name="dmlt_id" id="dmlt_id" type="hidden" value=""/>
                            <input name="action" id="action" type="hidden" value="add"/>
                            
                            <select name="sub_county" id="sub_county" class="form-control">
                                <option> -- Select Sub-County --</option>
                                <?php foreach ($districts as $dists) { ?>
                                    <option value="<?php echo $dists['id']; ?>"><?php echo $dists['district']; ?></option>
                                <?php } ?>
                            </select>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button type="button" id="add_dmlt_district" class="btn btn-primary add_dmlt_district">Save Changes</button>
                    </div>               

                </div>
<script type="text/javascript">
    $(function() {

        $('.dmlt_district').click(function(){    
          var id = $(this).attr('id');          
          var name =  $('td:nth-child(2)', $(this).parents('tr')).text(); 
          var label = 'Add Sub-County to '+name;                         
          $('#dmlt_sub_label').html(label);               
          $('#dmlt_id').val(id);               
        });

        $('#save_dmlt').click(function() {
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var phone = $('#phone').val();
            var district = $('#district').val();
            var county = $('#county').val();
            var email = $('#email').val();

            $.post("<?php echo base_url() . 'rtk_management/create_DMLT'; ?>", {
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone,
                district: district,
                county: county
            }).done(function(data) {
                alert("Data Loaded: " + data);
                $('#Add_DMLT').modal('hide');
                window.location = "<?php echo base_url() . 'rtk_management/county_admin/users'; ?>";
            });
        });
         $('#save_facility').click(function() {
            var first_name = $('#f_first_name').val();
            var last_name = $('#f_last_name').val();
            var phone = $('#f_phone').val();
            var district = $('#f_district').val();
            var facility = $('#f_facilities').val();
            var county = $('#f_county').val();
            var email = $('#f_email').val();

            $.post("<?php echo base_url() . 'rtk_management/create_MLT'; ?>", {
                first_name: first_name,
                last_name: last_name,
                email: email,
                phone: phone,
                district: district,
                facility: facility,
                county: county
            }).done(function(data) {
                alert("Data Loaded: " + data);
                $('#Add_Facility').modal('hide');
                window.location = "<?php echo base_url() . 'rtk_management/county_admin/users'; ?>";
            });
        });
         $('#users_table').dataTable(); 
        $('#users_table').tablecloth({theme: "paper",         
          bordered: true,
          condensed: true,
          striped: true,
          sortable: true,
          clean: true,
          cleanElements: "th td",
          customClass: "my-table"
        });

        $('.add_dmlt_district').click(function() {         
          var dmlt_id = $('#dmlt_id').val();
          var dmlt_district = $('#sub_county').val(); 
          var action= 'add';          
          $.post("<?php echo base_url() . 'rtk_management/dmlt_district_action'; ?>", {
            action : action,
            dmlt_id : dmlt_id,
            dmlt_district: dmlt_district, 
          }).done(function(data) {
            alert(data);
            $('dmlt_district'.dmlt_id).modal('hide');
            window.location = "<?php echo base_url() . 'rtk_management/county_admin/users'; ?>";
          });
          
        });         
                 
         // $('#f_district').change(function(){
         //   district_id = $('#f_district').val();
         //    // $("#f_facilities").empty();
         //    // if (district_id ==0) {
         //    //     $("#f_facilities").attr("disabled", true);
         //    // } else {
                

         //        $.ajax
         //          ({
         //            type: "POST",
         //            url: "rtk_management/get_facilities_district",
         //            data: district_id,
         //            cache: false,
         //            success: function(html)
         //          {
         //           console.log(html);   
         //           $("#f_facilities").html(html);
         //          },
         //          error: function(e){
         //          console.log(e.responseText);
         //           }
         //          });
         //   // alert(html);
         //        $("#f_facilities").attr("disabled", false);
         //    // }

         // });
    });
</script>
<style type="text/css">
table{
    font-size: 13px;
}
</style>