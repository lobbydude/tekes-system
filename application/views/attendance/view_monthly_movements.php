<?php
$emp_no = $this->session->userdata('username');
$user_role = $this->session->userdata('user_role');

$data_allowance = array(
    'Status' => 1
);
$this->db->where($data_allowance);
$q_allowance = $this->db->get('tbl_allowance');
foreach ($q_allowance->result() as $row_allowance) {
    $allowance_id = $row_allowance->A_Id;
    $allowance_name = $row_allowance->Allowance_Name;
    $allowance_amount = $row_allowance->Allowance_Amount;
    if ($allowance_id == 1) {
        $saturday_half_day = $allowance_amount;
    }
    if ($allowance_id == 2) {
        $saturday_full_day = $allowance_amount;
    }
    if ($allowance_id == 3) {
        $saturday_night = $allowance_amount;
    }
    if ($allowance_id == 4) {
        $sunday_full_day = $allowance_amount;
    }
    if ($allowance_id == 5) {
        $sunday_night = $allowance_amount;
    }
    if ($allowance_id == 6) {
        $both_day = $allowance_amount;
    }
    if ($allowance_id == 7) {
        $both_night = $allowance_amount;
    }
}
?>

<script src="<?php echo site_url('js/excel/jquery.btechco.excelexport.js') ?>"></script>
<script src="<?php echo site_url('js/excel/jquery.base64.js') ?>"></script>


<script>
    $(document).ready(function () {

        //removing loader

        $('footer .loading').remove();

        // for datasheet 
        $("#download_sheets").click(function () {
            $("#month_attendance_data1").btechco_excelexport({
                containerid: "month_attendance_data1",
                datatype: $datatype.Table
            });
        });


        // for getting month attendance

        <?php
                if ($this->uri->segment(3) != "" AND $this->uri->segment(4) != "") {
                    $months = $this->uri->segment(3);
                    $years = $this->uri->segment(4);
                } else {
                    $months = date("m");
                    $years = date("Y");
                }
        ?>
        
        







    });
</script>

<div class="main-content">
    <div class="container">

    <?php //if(!empty($subash)){ ?>
        <section class="topspace blackshadow bg-white"> 
            <div class="col-md-12">
                <div class="row">
                    <div class="panel-heading info-bar" >
                        <div class="panel-title">
                            <h2>Monthly Attendance Movements</h2>
                        </div>
                        <div class="panel-options">
                            <div class="row">
                                <div class="col-md-2"><span style="padding: 0.5em 3.5em 0.5em 1em;display: block;">Year</span></div>
                                <div class="col-md-4">
                                    <?php
                                    define('DOB_YEAR_START', 2000);
                                    $current_year = date('Y');
                                    ?>
                                    <select id="year_list" name="year_list" class="round" onchange="$('#change_timesheet').submit();">
                                        <?php
                                        if ($this->uri->segment(4) != "") {
                                            $cur_year = $this->uri->segment(4);
                                            for ($count = $current_year; $count >= DOB_YEAR_START; $count--) {
                                                ?>
                                                <option value="<?php echo $count; ?>" <?php
                                                if ($cur_year == $count) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $count; ?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    for ($count = $current_year; $count >= DOB_YEAR_START; $count--) {
                                                        ?>
                                                <option value="<?php echo $count; ?>"><?php echo $count; ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select> 
                                </div>
                                <div class="col-md-2"><span style="padding: 0.5em 3.5em 0.5em 1em;display: block;">Month</span></div>
                                <div class="col-md-4">
                                    <select class="round" id="month_list" name="month_list" onchange="location = this.options[this.selectedIndex].value + '/' + $('#year_list').val();">
                                        <?php
                                        if ($this->uri->segment(3) != "") {
                                            $cur_month = $this->uri->segment(3);
                                            $cur_month_name = date("F", mktime(0, 0, 0, $cur_month, 10));
                                            for ($m = 1; $m <= 12; $m++) {
                                                ?>
                                                <option value="<?php echo site_url('Attendance/view_monthly_move/' . $m); ?>" <?php
                                                if ($cur_month == $m) {
                                                    echo "selected=selected";
                                                }
                                                ?>>
                                                            <?php
                                                            if ($cur_month == $m) {
                                                                echo $cur_month_name;
                                                            } else {
                                                                // echo date('F');
                                                                echo date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                                                            }
                                                            ?>
                                                </option>
                                                <?php
                                            }
                                        } else {
                                            for ($m = 1; $m <= 12; $m++) {
                                                $current_month = date('m');
                                                $month = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                                                ?>
                                                <option value="<?php echo site_url('Attendance/view_monthly_move/' . $m); ?>" <?php
                                                if ($current_month == $m) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $month; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                    </select>
                                </div>
                                <!-- <div class="col-md-4">
                                    <a style="margin-top:0px" class="btn btn-primary btn-icon icon-left" id="timesheet_download_button">
                                        Download
                                        <i class="entypo-upload"></i>
                                    </a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <!-- Attendance Table Format Start Here -->
                    <div class="col-md-5">
                    <h2>View Monthly Timesheet</h2>
                    <p>
                    <a style="margin-top:0px;" class="btn btn-primary btn-icon icon-left" onclick="view_month_()">
                                        View Time sheet
                                        <i class="entypo-chart-bar"></i>
                    </a>
                    <img id="loader_img_sheet" src="<?php echo base_url('images/progress.gif');?>" style="display: none;width: 55px;">
                    </p>
                    </div>
                    <?php 

                    $this->db->where('Status',1);
                    $emp_number = $this->db->get('tbl_employee')->num_rows();

                    ?>
                    <div class="col-md-7">
                    <h2>Download Employee Monthly Attendance</h2>
                        <p>
                        <div class="col-sm-4">
                            <select class="round" id="from_employee"  onchange="get_emp_att()">
                                <option value="">From Employee</option>
                                <?php 
                                for($i=1;$i<$emp_number;$i++){
                                   echo "<option value=".$i.">$i</option>";
                                }
                                ?>

                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select class="round" id="to_employee" onchange="get_emp_att()">
                                <option value="">To Employee</option>
                                <?php 
                                for($i=1;$i<$emp_number;$i++){
                                   echo "<option value=".$i.">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4">

                            <a style="margin-top:0px;display: none;" class="btn btn-primary btn-icon icon-left download_sheets" id="download_sheets">Download<i class="entypo-download"></i></a>
                            <img id="loader_img" src="<?php echo base_url('images/progress.gif');?>" style="display: none;width: 23%;">
                            <a style="margin-top:0px;display: none;" class="btn btn-success btn-icon icon-left download_sheets" id="download_she" onclick="view_sheet()">View</i></a>
                        </div>
                        </p>
                    </div>
                    <!-- Attendance Table Format End Here -->
                </div>
            </div>
        </section>
        <!-- shwing time sheet -->
        <section class="topspace blackshadow bg-white"> 
            <div class="col-md-12">
                <div class="row"  id="timesheet_div">
                </div>
            </div>
        </section>

        <!-- shwing time sheet -->
        <section class="topspace blackshadow bg-white"> 
            <div class="col-md-12">
                <div class="row"  id="month_attendance_data1" style="overflow: auto;display:none;">
                </div>
            </div>
        </section>

        <script type="text/javascript">
            function get_emp_att(){

                $('.loading img').hide('');
               // var to_employee = $("#to_employee option:selected").val();
               // var from_employee = $("#from_employee option:selected").val();
               var to_employee = parseInt($('#to_employee').find(":selected").text());
               var from_employee = parseInt($('#from_employee').find(":selected").text());

               var error = null;

               //alert(to_employee+'  '+from_employee);

               if(to_employee!=='' && from_employee!==''){

                    if(to_employee > from_employee){

                        if((to_employee - from_employee) < 100 ){

                            $.ajax({

                                url : "<?php echo base_url('Attendance/attendance_month_data1/');?>",
                                type : "post",
                                data : {'month' : <?php echo $months;?>,'year' : <?php echo $years;?>,'from' :from_employee,'to' : to_employee},
                                beforeSend: function() {
                                    $('.download_sheets').hide();
                                    $('#loader_img').show();

                                },
                                success: function(html){
                                    $('#loader_img').hide();
                                    $('.download_sheets').show();
                                    $('#month_attendance_data1').html(html);
                                }

                            });

                        }else{

                            error = "Don't select more then 100";

                        }


                    }else{
                        error = "please select above to from employee";
                    }

               }else{
                error = "Please select two options";
               }

               if(error!==null){
                alert(error);
                $('.download_sheets').hide();
               }



            }

        function view_month_(){

             $('.loading img').hide('');

                $.ajax({

                    url : "<?php echo base_url('Attendance/view_month_sheet/');?>",
                    type : "post",
                    data : {'month' : <?php echo $months;?>,'year' : <?php echo $years;?>},
                    beforeSend: function() {
                        //$('#download_sheets').hide();
                        $('#loader_img_sheet').show();

                    },
                    success: function(html){

                        $('#loader_img_sheet').hide();

                        $('#timesheet_div').html(html);

                        //$('#download_sheets').show();
                        //$('#month_attendance_data1').html(html);
                    }

                });
        }

        function view_sheet(){
            
            var status = $('#month_attendance_data1:visible').length;

            if(status==0){
                $('#month_attendance_data1').show();
                $('#download_she').text('Hide');
            }else{
                $('#month_attendance_data1').hide();
                $('#download_she').text('View');

            }
        }

        </script>
    <?php //} ?>

        <!-- Table Script -->
        
        <!-- Download Table Content -->
       