if ($import_type == "Monthly_Statement") {
            if ($_FILES["import_payrollfile"]["size"] > 0) {
                $file = fopen($filename, "r");
                $sess_data = $this->session->all_userdata();
                $inserted_id = $sess_data['user_id'];
                $n = 1;

                while (($payslipData = fgetcsv($file, 10000, ",")) !== FALSE) {

                    if ($n != 1) {

                        //print_r($payslipData);die;

                        $empcode = $payslipData[1];
                        $year = $payslipData[2];
                        $month = $payslipData[3];
                        $employee_id = str_replace('DRN/', '', $empcode);
                        $data_payslip = array(
                            'Emp_Id' => $employee_id,
                            'Month' => $month,
                            'Year' => $year,
                            'Status' => 1
                        );
                        $this->db->where($data_payslip);
                        $q_payslip = $this->db->get('tbl_payslip_info');
                        $count_payslip = $q_payslip->num_rows();

                        $this->db->order_by('Sal_Id', 'desc');
                        $this->db->limit(1);
                        $data_salary = array(
                            'Employee_Id' => $employee_id,
                            'Status' => 1
                        );
                        $this->db->where($data_salary);
                        $q_salary = $this->db->get('tbl_salary_info');
                        foreach ($q_salary->Result() as $row_salary) {
                            $Monthly_CTC = number_format(($row_salary->Monthly_CTC), 2, '.', '');
                            $C_CTC = number_format(($row_salary->C_CTC), 2, '.', '');
                        }

                        $get_arrear_data = array(
                            'Emp_Id' => $employee_id,
                            'Month' => $month,
                            'Year' => $year,
                            'Status' => 1
                        );

                        $this->db->where($get_arrear_data);
                        $q_arrear_payslip = $this->db->get('tbl_payslip_arrear');
                        $count_arrear_payslip = $q_arrear_payslip->num_rows();
                        if ($count_arrear_payslip == 1) {
                            foreach ($q_arrear_payslip->result() as $row_arrear_payslip) {
                                $salary_arrears = filter_var(round(str_replace(',', '', $row_arrear_payslip->Net_Amount)), FILTER_SANITIZE_NUMBER_INT);
                            }
                        } else {
                            $salary_arrears = 0;
                        }

                        $no_of_days = $payslipData[4];
                        $disclop = $payslipData[5];
                        $leaveballop = $payslipData[6];
                        $lopoffered = $payslipData[7];
                        $no_of_days_lop = $disclop + $leaveballop + $lopoffered;
                        $additional_insurance = $payslipData[8];
                        $income_tax = $payslipData[9];
                        $deduction_others = $payslipData[10];
                        $salary_advance = $payslipData[11];
                        $attendance = $payslipData[12];
                        $night_shift = $payslipData[13];
                        $weekend = $payslipData[14];
                        $referal_bonus = $payslipData[15];
                        $additional_others = $payslipData[16];
                        $incentives = $payslipData[17];
                        $Basic = ($Monthly_CTC * 45) / 100;
                        if ($Basic >= 8500) {
                            $Basicpay = $Basic;
                        } else {
                            $Basicpay = 8500;
                        }
                        if ($C_CTC <= 250000) {
                            $Hra = ($Basicpay * 10) / 100;
                        } else {
                            $Hra = ($Basicpay * 40) / 100;
                        }
                        if ($Basicpay >= 8500) {
                            $Conveyance = ($Basicpay * 10) / 100;
                        } else {
                            $Conveyance = 800;
                        }
                        if ($C_CTC > 250000) {
                            $Medical = 1250;
                        } else {
                            $Medical = 0;
                        }
                        $Child_education = 0;
                        $Special_allowance = 0;
                        $Employer_PF_Amount = (($Basicpay + $Special_allowance) * 12) / 100;
                        if ($Employer_PF_Amount >= 1800) {
                            $Employer_PF = 1800;
                        } else {
                            $Employer_PF = $Employer_PF_Amount;
                        }
                        $Employer_ESI = 0;
                        $Total_Fixed_Gross = $Monthly_CTC - ($Employer_ESI + $Employer_PF);
                        if ($Total_Fixed_Gross <= 21000) {
                            $Employer_ESI = ($Total_Fixed_Gross * 4.75) / 100;
                        } else {
                            $Employer_ESI = 0;
                        }
                        $Total_Fixed_Gross = $Monthly_CTC - ($Employer_ESI + $Employer_PF);
                        if ($Total_Fixed_Gross - ($Basicpay + $Hra + $Conveyance + $Medical) < 0) {
                            $Skill_allowance = 0;
                        } else {
                            $Skill_allowance = $Total_Fixed_Gross - ($Basicpay + $Hra + $Conveyance + $Medical);
                        }
                        if ($Total_Fixed_Gross <= 21000) {
                            $Employer_ESI = ($Total_Fixed_Gross * 4.75) / 100;
                        } else {
                            $Employer_ESI = 0;
                        }
                        $Total_Fixed_Gross = $Monthly_CTC - ($Employer_ESI + $Employer_PF);
                        if ($Total_Fixed_Gross - ($Basicpay + $Hra + $Conveyance + $Medical) < 0) {
                            $Skill_allowance = 0;
                        } else {
                            $Skill_allowance = $Total_Fixed_Gross - ($Basicpay + $Hra + $Conveyance + $Medical);
                        }

                        $no_of_days_present = $no_of_days - $no_of_days_lop;
                        $monthly_basicpay = ($Basicpay / $no_of_days) * $no_of_days_present;
                        $monthly_hra = ($Hra / $no_of_days) * $no_of_days_present;
                        $monthly_conveyance = ($Conveyance / $no_of_days) * $no_of_days_present;
                        $monthly_skill_allowance = ($Skill_allowance / $no_of_days) * $no_of_days_present;
                        $monthly_medical = ($Medical / $no_of_days) * $no_of_days_present;
                        $monthly_child_education = ($Child_education / $no_of_days) * $no_of_days_present;
                        $monthly_special = ($Special_allowance / $no_of_days) * $no_of_days_present;
                        $total_actual_gross = $monthly_basicpay + $monthly_hra + $monthly_conveyance + $monthly_skill_allowance + $monthly_medical + $monthly_child_education + $monthly_special;
                        if ($Total_Fixed_Gross <= 21000) {
                            $monthly_employee_esi = ($total_actual_gross * 1.75) / 100;
                        } else {
                            $monthly_employee_esi = 0;
                        }
                        $monthly_employee_PF_amount = (($monthly_basicpay + $monthly_special) * 12) / 100;
                        if ($monthly_employee_PF_amount >= 1800) {
                            $monthly_employee_PF = 1800;
                        } else {
                            $monthly_employee_PF = $monthly_employee_PF_amount;
                        }
                        if ($Total_Fixed_Gross >= 15000) {
                            $monthly_prof_tax = 200;
                        } else {
                            $monthly_prof_tax = 0;
                        }
                        if ($Employer_ESI > 0) {
                            $monthly_insurance = 0;
                        } else {
                            if ($additional_insurance > 0) {
                                $monthly_insurance = 200 + $additional_insurance;
                            } else {
                                $monthly_insurance = 200;
                            }
                        }
                        $monthly_incometax = $income_tax;
                        $monthly_deduction_others = $deduction_others;
                        $monthly_salary_advance = $salary_advance;
                        $total_deduction = $monthly_employee_esi + $monthly_employee_PF + $monthly_prof_tax + $monthly_insurance + $monthly_incometax + $monthly_deduction_others + $monthly_salary_advance;
                        $total_income = $attendance + $salary_arrears + $night_shift + $weekend + $referal_bonus + $additional_others + $incentives;
                        $net_salary = $total_income + $total_actual_gross - $total_deduction;
                        $amount_words = $this->convert_number_to_words(round($net_salary));
                        $total_earnings = $total_income + $total_actual_gross;
                        $sess_data = $this->session->all_userdata();
                        $inserted_id = $sess_data['user_id'];
                        if ($count_payslip == 0) {
                            $insert_data = array(
                                'Emp_Id' => $employee_id,
                                'Month' => $month,
                                'Year' => $year,
                                'Monthly_CTC' => $Monthly_CTC,
                                'No_Of_Days' => $no_of_days,
                                'No_Of_Days_Worked' => $no_of_days_present,
                                'Disc_LOP' => $disclop,
                                'Leave_Balance_LOP' => $leaveballop,
                                'LOP_Offered_Date' => $lopoffered,
                                'No_Of_Days_LOP' => $no_of_days_lop,
                                'Basic' => number_format(round($monthly_basicpay), 2, '.', ','),
                                'HRA' => number_format(round($monthly_hra), 2, '.', ','),
                                'Conveyance' => number_format(round($monthly_conveyance), 2, '.', ','),
                                'Skill_Allowance' => number_format(round($monthly_skill_allowance), 2, '.', ','),
                                'Medical_Allowance' => number_format(round($monthly_medical), 2, '.', ','),
                                'Child_Education' => number_format(round($monthly_child_education), 2, '.', ','),
                                'Special_Allowance' => number_format(round($monthly_special), 2, '.', ','),
                                'Total_Gross' => number_format(round($total_actual_gross), 2, '.', ','),
                                'ESI_Employee' => number_format(round($monthly_employee_esi), 2, '.', ','),
                                'PF_Employee' => number_format(round($monthly_employee_PF), 2, '.', ','),
                                'Professional_Tax' => number_format(round($monthly_prof_tax), 2, '.', ','),
                                'Additioanl_Insurance' => number_format(round($additional_insurance), 2, '.', ','),
                                'Insurance' => number_format(round($monthly_insurance), 2, '.', ','),
                                'Income_Tax' => number_format(round($monthly_incometax), 2, '.', ','),
                                'Deduction_Others' => number_format(round($monthly_deduction_others), 2, '.', ','),
                                'Salary_Advance' => number_format(round($monthly_salary_advance), 2, '.', ','),
                                'Total_Deductions' => number_format(round($total_deduction), 2, '.', ','),
                                'Attendance_Allowance' => number_format(round($attendance), 2, '.', ','),
                                'Salary_Arrears' => number_format(round($salary_arrears), 2, '.', ','),
                                'Night_Shift_Allowance' => number_format(round($night_shift), 2, '.', ','),
                                'Weekend_Allowance' => number_format(round($weekend), 2, '.', ','),
                                'Referral_Bonus' => number_format(round($referal_bonus), 2, '.', ','),
                                'Additional_Others' => number_format(round($additional_others), 2, '.', ','),
                                'Incentives' => number_format(round($incentives), 2, '.', ','),
                                'Total_Income' => number_format(round($total_income), 2, '.', ','),
                                'Total_Earnings' => number_format(round($total_earnings), 2, '.', ','),
                                'Net_Amount' => number_format(round($net_salary), 2, '.', ','),
                                'Amount_Words' => ucwords($amount_words) . " Rupees Only",
                                'Inserted_By' => $inserted_id,
                                'Inserted_Date' => date('Y-m-d H:i:s'),
                                'Status' => 1
                            );


                            $this->db->insert('tbl_payslip_info', $insert_data);
                        } else {
                            foreach ($q_payslip->result() as $row_payslip) {
                                $payslip_id = $row_payslip->Payslip_Id;
                            }
                            $update_data = array(
                                'Emp_Id' => $employee_id,
                                'Month' => $month,
                                'Year' => $year,
                                'Monthly_CTC' => $Monthly_CTC,
                                'No_Of_Days' => $no_of_days,
                                'No_Of_Days_Worked' => $no_of_days_present,
                                'Disc_LOP' => $disclop,
                                'Leave_Balance_LOP' => $leaveballop,
                                'LOP_Offered_Date' => $lopoffered,
                                'No_Of_Days_LOP' => $no_of_days_lop,
                                'Basic' => number_format(round($monthly_basicpay), 2, '.', ','),
                                'HRA' => number_format(round($monthly_hra), 2, '.', ','),
                                'Conveyance' => number_format(round($monthly_conveyance), 2, '.', ','),
                                'Skill_Allowance' => number_format(round($monthly_skill_allowance), 2, '.', ','),
                                'Medical_Allowance' => number_format(round($monthly_medical), 2, '.', ','),
                                'Child_Education' => number_format(round($monthly_child_education), 2, '.', ','),
                                'Special_Allowance' => number_format(round($monthly_special), 2, '.', ','),
                                'Total_Gross' => number_format(round($total_actual_gross), 2, '.', ','),
                                'ESI_Employee' => number_format(round($monthly_employee_esi), 2, '.', ','),
                                'PF_Employee' => number_format(round($monthly_employee_PF), 2, '.', ','),
                                'Professional_Tax' => number_format(round($monthly_prof_tax), 2, '.', ','),
                                'Additioanl_Insurance' => number_format(round($additional_insurance), 2, '.', ','),
                                'Insurance' => number_format(round($monthly_insurance), 2, '.', ','),
                                'Income_Tax' => number_format(round($monthly_incometax), 2, '.', ','),
                                'Deduction_Others' => number_format(round($monthly_deduction_others), 2, '.', ','),
                                'Salary_Advance' => number_format(round($monthly_salary_advance), 2, '.', ','),
                                'Total_Deductions' => number_format(round($total_deduction), 2, '.', ','),
                                'Attendance_Allowance' => number_format(round($attendance), 2, '.', ','),
                                'Salary_Arrears' => number_format(round($salary_arrears), 2, '.', ','),
                                'Night_Shift_Allowance' => number_format(round($night_shift), 2, '.', ','),
                                'Weekend_Allowance' => number_format(round($weekend), 2, '.', ','),
                                'Referral_Bonus' => number_format(round($referal_bonus), 2, '.', ','),
                                'Additional_Others' => number_format(round($additional_others), 2, '.', ','),
                                'Incentives' => number_format(round($incentives), 2, '.', ','),
                                'Total_Income' => number_format(round($total_income), 2, '.', ','),
                                'Total_Earnings' => number_format(round($total_earnings), 2, '.', ','),
                                'Net_Amount' => number_format(round($net_salary), 2, '.', ','),
                                'Amount_Words' => ucwords($amount_words) . " Rupees Only",
                                'Inserted_By' => $inserted_id,
                                'Inserted_Date' => date('Y-m-d H:i:s'),
                                'Status' => 1
                            );

                            $this->db->where('Payslip_Id', $payslip_id);
                            $this->db->update('tbl_payslip_info', $update_data);
                        }
                    }
                    $n++;
                }
                echo "success";
            }
        }