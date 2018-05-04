<?php 

$month = $cur_month;
$year  = $cur_year;
//$cur_month_name;
$num   = $days_in_month;
$emp_no = $this->session->userdata('username');
$user_role = $this->session->userdata('user_role');

?>
<!-- Table Script -->
<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/freeze_table/jquery.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo site_url('css/freeze_table/fixedColumns.dataTables.min.css') ?>">
<script src="<?php echo site_url('css/freeze_table/dataTables.fixedColumns.min.js') ?>"></script>
<script type="text/javascript">
                                var responsiveHelper;
                                var breakpointDefinition = {
                                    tablet: 1024, phone: 480
                                };
                                var tableContainer;
                                jQuery(document).ready(function ($) {
                                    tableContainer = $("#timesheet_table");
                                    tableContainer.dataTable({
                                        "scrollY": 400,
                                        "scrollX": true,
                                        fixedColumns: {
                                            leftColumns: 2
                                        },
                                        "sPaginationType": "bootstrap",
                                        "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                                        "bStateSave": true,
                                        // Responsive Settings
                                        bAutoWidth: false,
                                        fnPreDrawCallback: function () {
                                            // Initialize the responsive datatables helper once.
                                            if (!responsiveHelper) {
                                                responsiveHelper = new ResponsiveDatatablesHelper(tableContainer, breakpointDefinition);
                                            }
                                        },
                                        fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                                            responsiveHelper.createExpandIcon(nRow);
                                        },
                                        fnDrawCallback: function (oSettings) {
                                            responsiveHelper.respond();
                                        }
                                    });

                                    $(".dataTables_wrapper select").select2({
                                        minimumResultsForSearch: -1
                                    });
                                });
</script>

<!-- Attendance Table Format Start Here -->
<table class="table table-bordered stripe row-border order-column" id="timesheet_table">
    <div class="panel-body">
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn btn-blue">H</i> Holiday</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn btn-red">A</i> Absent</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn btn-warning">WO</i> Weekly Off</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn btn-success">P/NP</i>Present</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn" style="background-color: #00FFFF">HP</i> Half Day Present</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn" style="background-color: #BF00FF;color:#fff">LOP</i> LOP</a></div>
        <div class="icon-el col-md-1 col-sm-2"><a href="#"><i class="btn" style="background-color: #58FAD0;color:#000">COMP-OFF</i> COMP-OFF</a></div>
    </div>
    <h3 class="col-sm-8" id="curr_div">Daily Attendance for the Month of <?php echo $cur_month_name . " " . $year ?></h3>
    <thead> 
        <tr>
            <th>Employee Code</th>
            <th>Employees</th>
            <th>DOJ</th>
            <th>No of Days</th>
            <?php
            for ($i = 1; $i <= $num; $i++) {
                $mktime = mktime(0, 0, 0, $month, $i, $year);
                $date = date("d", $mktime);
                $dates_month[$i] = $date;
                $date_n = date("d-m-Y", $mktime);
                ?>
                <th><p class='vertical-align'><?php echo $date_n; ?></p></th>
        <?php
    }
    ?>
    </tr>
    </thead>
    <tbody>
        <?php
        if ($user_role == 2 || $user_role == 6) {
            $i = 1;
            $data = array(
                'Status' => 1
            );
            $this->db->where($data);
            $q = $this->db->get('tbl_employee');
            foreach ($q->result() as $row) {
                $emp_firstname = $row->Emp_FirstName;
                $emp_middlename = $row->Emp_MiddleName;
                $emp_lastname = $row->Emp_LastName;
                $employee_no = $row->Emp_Number;
                $doj = $row->Emp_Doj;
                $emp_doj = date("d-m-Y", strtotime($doj));
                $interval = date_diff(date_create(), date_create($doj));
                $no_days = $interval->format("%a");
                $this->db->where('employee_number', $employee_no);
                $q_code = $this->db->get('tbl_emp_code');
                foreach ($q_code->Result() as $row_code) {
                    $emp_code = $row_code->employee_code;
                }
                if ($no_days > 89) {
                    $weekend_eligibility = "YES";
                } else {
                    $weekend_eligibility = "NO";
                }
                ?>
                <tr>
                    <td><?php echo $emp_code . $employee_no; ?></td>
                    <td><?php echo $emp_firstname . " " . $emp_lastname . " " . $emp_middlename; ?></td>
                    <td><?php echo $emp_doj; ?></td>
                    <td><?php echo $no_days; ?></td>
                    <?php
                    $p = 0;
                    $a = 0;
                    $wp = 0;
                    $wo = 0;
                    $h = 0;
                    $hp = 0;
                    $no_of_w_days = 0;
                    $lop = 0;
                    $dis_lop = 0;
                    $week_half_day = 0;
                    $week_satfull_day = 0;
                    $week_satnightfull_day = 0;
                    $week_sunfull_day = 0;
                    $week_sunnightfull_day = 0;
                    $week_both_day = 0;
                    $week_both_night = 0;
                    $comp_off_availed = 0;
                    $weekend_probationer_total = 0;
                    $weekend_amount_total = 0;
                    $day_shift_count = 0;
                    $night_shift_count = 0;
                    $comp_off_taken = 0;
                    $total_hours = 0;
                    if ($this->uri->segment(3) != "" AND $this->uri->segment(4) != "") {
                        $month = $this->uri->segment(3);
                        $cur_month_name = date("F", mktime(0, 0, 0, $month, 10));
                        $year = $this->uri->segment(4);
                        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    }
                    $no_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    for ($i = 1; $i <= $num; $i++) {
                        $mktime = mktime(0, 0, 0, $month, $i, $year);
                        $date = date("d", $mktime);
                        $dates_month[$i] = $date;
                        $date_1 = date("d-m-Y", $mktime);
                        $dates_month_1 = date("Y-m-d", $mktime);
                        $curr_date_calender = strtotime(date('d-m-Y'));
                        $calender_date = strtotime($date_1);
                        if ($curr_date_calender > $calender_date) {
                            $data_compoff = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'Comp Off',
                                'Approval' => 'Yes',
                                'Status' => 1
                            );
                            $this->db->where($data_compoff);
                            $q_compoff = $this->db->get('tbl_attendance_mark');
                            $count_compoff = $q_compoff->num_rows();
                            foreach ($q_compoff->result() as $row_compoff) {
                                $compoff_id = $row_compoff->A_M_Id;
                            }
                            $data_lop = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'LOP',
                                'Status' => 1
                            );
                            $this->db->where($data_lop);
                            $q_lop = $this->db->get('tbl_attendance_mark');
                            $count_lop = $q_lop->num_rows();
                            foreach ($q_lop->result() as $row_lop) {
                                $lop_id = $row_lop->A_M_Id;
                            }
                            $data_dislop = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'Disciplinary LOP',
                                'Status' => 1
                            );
                            $this->db->where($data_dislop);
                            $q_dislop = $this->db->get('tbl_attendance_mark');
                            $count_dislop = $q_dislop->num_rows();
                            foreach ($q_dislop->result() as $row_dislop) {
                                $dislop_id = $row_dislop->A_M_Id;
                            }
                            if ($count_compoff == 1) {
                                $comp_off_text = "Comp_Off";
                                echo "<td style = 'background-color:#58FAD0;color:#000'>";
                                echo "COMP-OFF";
                                echo "</td>";
                            } else if ($count_lop == 1) {
                                $lop_text = "Lop";
                                echo "<td style = 'background-color:#BF00FF;color:#fff'>";
                                echo "LOP";
                                echo "</td>";
                            } else if ($count_dislop == 1) {
                                $dis_lop_text = "Disciplinary_LOP";
                                echo "<td style = 'background-color:#BF00FF;color:#fff'>";
                                echo "Disciplinary LOP";
                                echo "</td>";
                            } else {
                                $dat_no_1 = date('N', strtotime($date_1));
                                if ($dat_no_1 == 6 || $dat_no_1 == 7) {
                                    $data_in_weekend = array(
                                        'Emp_Id' => $employee_no,
                                        'Login_Date' => $dates_month_1,
                                        'Status' => 1
                                    );
                                    $this->db->where($data_in_weekend);
                                    $q_in_weekend = $this->db->get('tbl_attendance');
                                    $count_in_weekend = $q_in_weekend->num_rows();
                                    if ($count_in_weekend == 1) {
                                        foreach ($q_in_weekend->result() as $row_in_weekend) {
                                            $A_Id_in_weekend = $row_in_weekend->A_Id;
                                            $Login_Date1_weekend = $row_in_weekend->Login_Date;
                                            $Login_Date = date("d-m-Y", strtotime($Login_Date1_weekend));
                                            $Login_Time_weekend = $row_in_weekend->Login_Time;
                                            $shift_name_weekend = $row_in_weekend->Shift_Name;
                                            $Logout_Date1_weekend = $row_in_weekend->Logout_Date;
                                            $Logout_Date_weekend = date("d-m-Y", strtotime($Logout_Date1_weekend));
                                            $Logout_Time_weekend = $row_in_weekend->Logout_Time;
                                            $h1_weekend = strtotime($Login_Time_weekend);
                                            $h2_weekend = strtotime($Logout_Time_weekend);
                                            $seconds_weekend = $h2_weekend - $h1_weekend;
                                            $total_hours_weekend = gmdate("H:i:s", $seconds_weekend);
                                            $min_time_weekend = "04:30:00";
                                            if ($total_hours_weekend > $min_time_weekend) {
                                                echo "<td style = 'background-color:#00a651;color:#fff'>";
                                                if ($shift_name_weekend == "NIGHT -1" || $shift_name_weekend == "NIGHT -2") {
                                                    echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                } else {
                                                    echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                }
                                                echo "</td>";
                                            } else {
                                                echo "<td style = 'background-color:#00a651'>";
                                                echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                echo "</td>";
                                            }
                                        }
                                    } else {
                                        if ($dat_no_1 == 6) {
                                            echo "<td style = 'background-color:#fad839'>";
                                            echo "SAT";
                                            echo "</td>";
                                        }if ($dat_no_1 == 7) {
                                            echo "<td style = 'background-color:#fad839'>";
                                            echo "SUN";
                                            echo "</td>";
                                        }
                                    }
                                } else {
                                    $holiday_data = array(
                                        'Holiday_Date' => $dates_month_1,
                                        'Status' => 1
                                    );
                                    $this->db->where($holiday_data);
                                    $q_hol = $this->db->get('tbl_holiday');
                                    $count_hol = $q_hol->num_rows();
                                    if ($count_hol == 1) {
                                        echo "<td style = 'background-color:#0072bc;color:#fff'>";
                                        echo "H";
                                        echo "</td>";
                                    } else {
                                        $data_in = array(
                                            'Emp_Id' => $employee_no,
                                            'Login_Date' => $dates_month_1,
                                            'Status' => 1
                                        );
                                        $this->db->where($data_in);
                                        $q_in = $this->db->get('tbl_attendance');
                                        $count_in = $q_in->num_rows();
                                        if ($count_in == 1) {
                                            foreach ($q_in->result() as $row_in) {
                                                $A_Id_in = $row_in->A_Id;
                                                $Login_Date1 = $row_in->Login_Date;
                                                $Login_Date = date("d-m-Y", strtotime($Login_Date1));
                                                $Login_Time = $row_in->Login_Time;
                                                $shift_name = $row_in->Shift_Name;

                                                $Logout_Date1 = $row_in->Logout_Date;
                                                $Logout_Date = date("d-m-Y", strtotime($Logout_Date1));
                                                $Logout_Time = $row_in->Logout_Time;

                                                $h1 = strtotime($Login_Time);
                                                $h2 = strtotime($Logout_Time);
                                                $seconds = $h2 - $h1;
                                                $total_hours_present = gmdate("H:i:s", $seconds);
                                                $min_time = "04:30:00";
                                                if ($total_hours_present > $min_time) {
                                                    echo "<td style = 'background-color:#00a651;color:#fff'>";
                                                    if ($shift_name == "NIGHT -1" || $shift_name == "NIGHT -2") {
                                                        echo "$Login_Time : $Logout_Time";
                                                    } else {
                                                        echo "$Login_Time : $Logout_Time";
                                                    }
                                                    echo "</td>";
                                                } else {
                                                    echo "<td style = 'background-color:#00FFFF'>";
                                                    echo "$Login_Time : $Logout_Time";
                                                    echo "</td>";
                                                }
                                            }
                                        } else {
                                            echo "<td style = 'background-color:#d42020;color: #fff;'>";
                                            echo "A";
                                            echo "</td>";
                                        }
                                    }
                                }
                            }
                        } else {
                            echo "<td style = 'background-color:#d42020;color:#fff'>";
                            echo "-";
                            echo "</td>";
                        }
                    }
                    ?>
                </tr>
                <?php
                $i++;
            }
        }
		
        if ($user_role == 1) {
            $i = 1;
            $this->db->group_by('Employee_Id');
            $data_report = array(
                'Reporting_To' => $emp_no,
                'Status' => 1
            );
            $this->db->where($data_report);
            $q_emp_report = $this->db->get('tbl_employee_career');
            foreach ($q_emp_report->Result() as $row_emp_report) {
                $employee_id = $row_emp_report->Employee_Id;
                $data = array(
                    'Emp_Number' => $employee_id,
                    'Status' => 1
                );
                $this->db->where($data);
                $q = $this->db->get('tbl_employee');
                foreach ($q->result() as $row) {
                    $emp_firstname = $row->Emp_FirstName;
                    $emp_middlename = $row->Emp_MiddleName;
                    $emp_lastname = $row->Emp_LastName;
                    $employee_no = $row->Emp_Number;
                    $doj = $row->Emp_Doj;
                    $emp_doj = date("d-m-Y", strtotime($doj));
                    $interval = date_diff(date_create(), date_create($doj));
                    $no_days = $interval->format("%a");
                    $this->db->where('employee_number', $employee_no);
                    $q_code = $this->db->get('tbl_emp_code');
                    foreach ($q_code->Result() as $row_code) {
                        $emp_code = $row_code->employee_code;
                    }
                    if ($no_days > 89) {
                        $weekend_eligibility = "YES";
                    } else {
                        $weekend_eligibility = "NO";
                    }
                    ?>
                    <tr>
                        <td><?php echo $emp_code . $employee_no; ?></td>
                        <td><?php echo $emp_firstname . " " . $emp_lastname . " " . $emp_middlename; ?></td>
                        <td><?php echo $emp_doj; ?></td>
                        <td><?php echo $no_days; ?></td>
                        <?php
                        $p = 0;
                        $a = 0;
                        $wp = 0;
                        $wo = 0;
                        $h = 0;
                        $hp = 0;
                        if ($this->uri->segment(3) != "" AND $this->uri->segment(4) != "") {
                            $month = $this->uri->segment(3);
                            $cur_month_name = date("F", mktime(0, 0, 0, $month, 10));
                            $year = $this->uri->segment(4);
                            $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        }

                        $no_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                        for ($i = 1; $i <= $num; $i++) {
                            $mktime = mktime(0, 0, 0, $month, $i, $year);
                            $date = date("d", $mktime);
                            $dates_month[$i] = $date;

                            $date_1 = date("d-m-Y", $mktime);
                            $dates_month_1 = date("Y-m-d", $mktime);

                            $data_compoff = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'Comp Off',
                                'Approval' => 'Yes',
                                'Status' => 1
                            );
                            $this->db->where($data_compoff);
                            $q_compoff = $this->db->get('tbl_attendance_mark');
                            $count_compoff = $q_compoff->num_rows();

                            $data_lop = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'LOP',
                                'Status' => 1
                            );
                            $this->db->where($data_lop);
                            $q_lop = $this->db->get('tbl_attendance_mark');
                            $count_lop = $q_lop->num_rows();

                            $data_dislop = array(
                                'Emp_Id' => $employee_no,
                                'Date' => $dates_month_1,
                                'Type' => 'Disciplinary LOP',
                                'Status' => 1
                            );
                            $this->db->where($data_dislop);
                            $q_dislop = $this->db->get('tbl_attendance_mark');
                            $count_dislop = $q_dislop->num_rows();

                            if ($count_compoff == 1) {
                                echo "<td style = 'background-color:#58FAD0;color:#000'>COMP-OFF</td>";
                            } else if ($count_lop == 1) {
                                echo "<td style = 'background-color:#BF00FF;color:#fff'>LOP</td>";
                            } else if ($count_dislop == 1) {
                                echo "<td style = 'background-color:#BF00FF;color:#fff'>Disciplinary LOP</td>";
                            } else {
                                $dat_no_1 = date('N', strtotime($date_1));
                                if ($dat_no_1 == 6 || $dat_no_1 == 7) {
                                    $data_in_weekend = array(
                                        'Emp_Id' => $employee_no,
                                        'Login_Date' => $dates_month_1,
                                        'Status' => 1
                                    );
                                    $this->db->where($data_in_weekend);
                                    $q_in_weekend = $this->db->get('tbl_attendance');
                                    $count_in_weekend = $q_in_weekend->num_rows();
                                    if ($count_in_weekend == 1) {
                                        foreach ($q_in_weekend->result() as $row_in_weekend) {
                                            $A_Id_in_weekend = $row_in_weekend->A_Id;
                                            $Login_Date1_weekend = $row_in_weekend->Login_Date;
                                            $Login_Date = date("d-m-Y", strtotime($Login_Date1));
                                            $Login_Time_weekend = $row_in_weekend->Login_Time;
                                            $shift_name_weekend = $row_in_weekend->Shift_Name;
                                            $Logout_Date1_weekend = $row_in_weekend->Logout_Date;
                                            $Logout_Date_weekend = date("d-m-Y", strtotime($Logout_Date1_weekend));
                                            $Logout_Time_weekend = $row_in_weekend->Logout_Time;

                                            $h1_weekend = strtotime($Login_Time_weekend);
                                            $h2_weekend = strtotime($Logout_Time_weekend);
                                            $seconds_weekend = $h2_weekend - $h1_weekend;
                                            $total_hours_weekend = gmdate("H:i:s", $seconds_weekend);
                                            $min_time_weekend = "04:30:00";
                                            if ($total_hours_weekend > $min_time_weekend) {
                                                echo "<td style = 'background-color:#00a651;color:#fff'>";

                                                if ($shift_name_weekend == "NIGHT -1" || $shift_name_weekend == "NIGHT -2") {
                                                    echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                } else {
                                                    echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                }
                                                echo "</td>";
                                            } else {
                                                echo "<td style = 'background-color:#00a651'>";
                                                echo "$Login_Time_weekend : $Logout_Time_weekend";
                                                echo "</td>";
                                            }
                                        }
                                    } else {
                                        if ($dat_no_1 == 6) {
                                            echo "<td style = 'background-color:#fad839'>SAT</td>";
                                        }if ($dat_no_1 == 7) {
                                            echo "<td style = 'background-color:#fad839'>SUN</td>";
                                        }
                                    }
                                } else {
                                    $holiday_data = array(
                                        'Holiday_Date' => $dates_month_1,
                                        'Status' => 1
                                    );
                                    $this->db->where($holiday_data);
                                    $q_hol = $this->db->get('tbl_holiday');
                                    $count_hol = $q_hol->num_rows();
                                    if ($count_hol == 1) {
                                        echo "<td style = 'background-color:#0072bc;color:#fff'>H</td>";
                                    } else {
                                        $data_in = array(
                                            'Emp_Id' => $employee_no,
                                            'Login_Date' => $dates_month_1,
                                            'Status' => 1
                                        );
                                        $this->db->where($data_in);
                                        $q_in = $this->db->get('tbl_attendance');
                                        $count_in = $q_in->num_rows();
                                        if ($count_in == 1) {
                                            foreach ($q_in->result() as $row_in) {
                                                $A_Id_in = $row_in->A_Id;
                                                $Login_Date1 = $row_in->Login_Date;
                                                $Login_Date = date("d-m-Y", strtotime($Login_Date1));
                                                $Login_Time = $row_in->Login_Time;
                                                $shift_name = $row_in->Shift_Name;

                                                $Logout_Date1 = $row_in->Logout_Date;
                                                $Logout_Date = date("d-m-Y", strtotime($Logout_Date1));
                                                $Logout_Time = $row_in->Logout_Time;

                                                $h1 = strtotime($Login_Time);
                                                $h2 = strtotime($Logout_Time);
                                                $seconds = $h2 - $h1;
                                                $total_hours_present = gmdate("H:i:s", $seconds);
                                                $min_time = "04:30:00";
                                                if ($total_hours_present > $min_time) {
                                                    echo "<td style = 'background-color:#00a651;color:#fff'>";
                                                    if ($shift_name == "NIGHT -1" || $shift_name == "NIGHT -2") {
                                                        echo "$Login_Time : $Logout_Time";
                                                    } else {
                                                        echo "$Login_Time : $Logout_Time";
                                                    }
                                                    echo "</td>";
                                                } else {
                                                    echo "<td style = 'background-color:#00FFFF'>";
                                                    echo "$Login_Time : $Logout_Time";
                                                    echo "</td>";
                                                }
                                            }
                                        } else {
                                            echo "<td style = 'background-color:#d42020;color:#fff'>A</td>";
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                    </tr>
                    <?php
                    $i++;
                }
            }
        }
        ?>
    </tbody>
</table>
<!-- Attendance Table Format End Here -->