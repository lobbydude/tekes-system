<?php
$this->db->where('JP_Id', $JP_Id);
$q = $this->db->get('tbl_jobpost_requisitions');
foreach ($q->result() as $row) {
    $edit_jobpost_interview_date = $row->Interview_Date;
    $edit_jobpost_department_name = $row->Department_Name;
    $edit_jobpost_subdepartment_name = $row->Sub_Department_Name;
    $edit_jobpost_title_name = $row->Job_Title;
    $edit_jobpost_skills = $row->Job_Skills;
    $edit_jobpost_type = $row->Job_Type;    
    $edit_jobpost_positions = $row->Positions;
    $edit_jobpost_location = $row->Job_Location;
    $edit_jobpost_qualification = $row->Qualification;
    $edit_jobpost_experience = $row->Experience;
    $edit_jobpost_candidate_age_start = $row->Age_Start;
    $edit_jobpost_candidate_age_end = $row->Age_End;
    $edit_jobpost_salary_start_range = $row->Salary_Start_Range;
    $edit_jobpost_salary_end_range = $row->Salary_End_Range;
    $edit_jobpost_gender = $row->Gender;
    $edit_jobpost_shift_time = $row->Shift_Time;
    $edit_jobpost_jobdescription = $row->Job_Description;
    $edit_jobpost_otherinformation = $row->Job_Other_Information;    
}
?>
<script>
    $(document).ready(function () {
        $('#editjobpost_Requisitions_form').submit(function (e) {
            e.preventDefault();
            var formdata = {
                edit_jobpost_id: $('#edit_jobpost_id').val(),
                edit_jobpost_interview_date: $('#edit_jobpost_interview_date').val(),
                edit_jobpost_department_name: $('#edit_jobpost_department_name').val(),
                edit_jobpost_subdepartment_name: $('#edit_jobpost_subdepartment_name').val(),
                edit_jobpost_title_name: $('#edit_jobpost_title_name').val(),
                edit_jobpost_skills: $('#edit_jobpost_skills').val(),
                edit_jobpost_type: $('#edit_jobpost_type').val(),
                edit_jobpost_positions: $('#edit_jobpost_positions').val(),
                edit_jobpost_location: $('#edit_jobpost_location').val(),
                edit_jobpost_qualification: $('#edit_jobpost_qualification').val(),
                edit_jobpost_experience: $('#edit_jobpost_experience').val(),
                edit_jobpost_candidate_age_start: $('#edit_jobpost_candidate_age_start').val(),
                edit_jobpost_candidate_age_end: $('#edit_jobpost_candidate_age_end').val(),
                edit_jobpost_salary_start_range: $('#edit_jobpost_salary_start_range').val(),
                edit_jobpost_salary_end_range: $('#edit_jobpost_salary_end_range').val(),                
                edit_jobpost_gender: $('#edit_jobpost_gender').val(),
                edit_jobpost_shift_time: $('#edit_jobpost_shift_time').val(),
                edit_jobpost_jobdescription: $('#edit_jobpost_jobdescription').val(),                
                edit_jobpost_otherinformation: $('#edit_jobpost_otherinformation').val()                  
            };
            $.ajax({
                url: "<?php echo site_url('Recruitment/edit_jobpost') ?>",
                type: 'post',
                data: formdata,
                success: function (msg) {
                    if (msg == 'fail') {
                        $('#edit_jobpost_error').show();
                    }
                    if (msg.trim() == 'success') {
                        $('#edit_jobpost_success').show();
                        window.location.reload();
                    }
                    else if (msg.trim() == 'fail' && msg != 'success') {
                        $('#edit_jobpost_server_error').html(msg);
                        $('#edit_jobpost_server_error').show();
                    }
                }
            });
        });
    });

</script>

<div class="modal-body">
    <div class="row">
        <div class="col-md-10">
            <div id="edit_jobpost_server_error" class="alert alert-info" style="display:none;"></div>
            <div id="edit_jobpost_success" class="alert alert-success" style="display:none;">Job Post details updated successfully.</div>
            <div id="edit_jobpost_error" class="alert alert-danger" style="display:none;">Failed to update Job Post details.</div>
        </div>
    </div>
        <input type="hidden" name="edit_jobpost_id" id="edit_jobpost_id" value="<?php echo $JP_Id; ?>">        
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-2" class="control-label">Date</label>
                    <div class="input-group">
                        <input type="text" name="edit_jobpost_interview_date" id="edit_jobpost_interview_date" class="form-control datepicker" data-format="dd-mm-yyyy" value="<?php echo $edit_jobpost_interview_date; ?>">
                        <div class="input-group-addon">
                            <a href="#"><i class="entypo-calendar"></i></a>
                        </div>
                    </div>                                     
                </div>	
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-3" class="control-label">Department Name</label>
                    <select id="edit_jobpost_department_name" name="edit_jobpost_department_name" class="round" data-validate="required" data-message-required="Please select department name.">
                        <option value="">Select Department</option>
                        <?php
                        $this->db->order_by('J_Id', 'desc');
                        $this->db->group_by('Department_Name');
                        $this->db->where('Status', 1);
                        $q_department = $this->db->get('tbl_jobtitle');
                        foreach ($q_department->Result() as $row_department) {
                            $department_jobtitle = $row_department->Department_Name;
                            $department_id = $row_department->S_Id;
                            // Department Name get the value
                            $this->db->where('Department_Id', $department_jobtitle);
                            $q_dept = $this->db->get('tbl_department');
                            foreach ($q_dept->result() as $row_dept) {
                                $department_name = $row_dept->Department_Name;
                            }
                            ?>
                            <option value="<?php echo $department_jobtitle; ?>" <?php
                            if ($edit_jobpost_department_name == $department_jobtitle) {
                                echo "selected=selected";
                            }
                            ?>>
                            <?php echo $department_name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-3" class="control-label">Sub Department Name</label>
                    <select id="edit_jobpost_subdepartment_name" name="edit_jobpost_subdepartment_name" class="round" data-validate="required" data-message-required="Please select department.">
                        <option value="">Select Sub Department</option>
                        <?php
                        $this->db->order_by('J_Id', 'desc');
                        $this->db->group_by('Subdepartment_Name');
                        $this->db->where('Status', 1);
                        $q_subdepartment = $this->db->get('tbl_jobtitle');
                        foreach ($q_subdepartment->Result() as $row_subdepartment) {
                            $department_name = $row_subdepartment->Department_Name;
                            $subdepartment_name1 = $row_subdepartment->Subdepartment_Name;
                            $subdepartment_id = $row_subdepartment->S_Id;
                            // Sub Department Name get the value
                            $this->db->where('Subdepartment_Id', $subdepartment_name1);
                            $q_subdept = $this->db->get('tbl_subdepartment');
                            foreach ($q_subdept->result() as $row_subdept) {
                                $subdepartment_name = $row_subdept->Subdepartment_Name;
                            }
                            ?>
                            <option value="<?php echo $subdepartment_name1; ?>" <?php
                            if ($edit_jobpost_subdepartment_name == $subdepartment_name1) {
                                echo "selected=selected";
                            }
                            ?>>
                            <?php echo $subdepartment_name; ?></option>  
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-1" class="control-label">Job Title</label>
                    <select name="edit_jobpost_title_name" id="edit_jobpost_title_name" class="round" data-validate="required" data-message-required="Please Select jobtitle">
                        <option value="">Select Jobtitle</option>
                        <?php
                        $this->db->order_by('Designation_Id', 'desc');
                        $this->db->group_by('Designation_Name');
                        $this->db->where('Status', 1);
                        $q_designation = $this->db->get('tbl_designation');
                        foreach ($q_designation->Result() as $row_designation) {
                            $designation_name = $row_designation->Designation_Name;
                            $designation_id = $row_designation->Designation_Id;
                            $role = $row_designation->Role;
                            ?>                            
                            <option value="<?php echo $designation_id; ?>" <?php
                            if ($edit_jobpost_title_name == $designation_id) {
                                echo "selected=selected";
                            }
                            ?>>
                            <?php echo $designation_name; ?></option>                         
                            <?php
                        }
                        ?>
                    </select>
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">Job Skills</label>
                    <select name="edit_jobpost_skills" id="edit_jobpost_skills" class="round" data-validate="required" data-message-required="Please Select jobtitle">
                        <option value="">Select Jobskills</option>
                        <?php
                        $this->db->order_by('S_Id', 'desc');
                        $this->db->group_by('Jobskills');
                        $this->db->where('Status', 1);
                        $q_skills = $this->db->get('tbl_jobskills');
                        foreach ($q_skills->Result() as $row_skills) {
                            $skills_id = $row_skills->S_Id;
                            $skill_jobtitle = $row_skills->Jobtitle;
                            $skill_jobskills = $row_skills->Jobskills;
                            ?>
                            <option value="<?php echo $skill_jobskills; ?>" <?php
                            if ($edit_jobpost_skills == $skill_jobskills) {
                                echo "selected=selected";
                            }
                            ?>>
                            <?php echo $skill_jobskills;?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>	
            </div>                                     
        </div><!-- first row Close-->
								
        <div class="row"><!-- Second row Start-->                               
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Job Type</label>
                    <select id="edit_jobpost_type" name="edit_jobpost_type" class="round" data-validate="required">
                        <option value="<?php echo $edit_jobpost_type; ?>"><?php echo $edit_jobpost_type; ?></option>
                        <option value="">Select Jobtype</option>
                        <option value="Permanent">Permanent</option>
                        <option value="Contract">Contract</option>
                        <option value="Project Based">Project Based</option>
                        <option value="Other">Other</option>                                            
                    </select>
                </div>
            </div>                                    
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-3" class="control-label">No.of.Positions</label>
                    <input type="text" name="edit_jobpost_positions" id="edit_jobpost_positions" class="form-control" data-validate="required" value="<?php echo $edit_jobpost_positions; ?>">
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Job Location</label>
                    <input type="text" name="edit_jobpost_location" id="edit_jobpost_location" class="form-control" data-validate="required" data-message-required="Please enter Job Location" value="<?php echo $edit_jobpost_location;?>">
                </div>	
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-3" class="control-label">Candidate Qualification</label>
                    <input type="text" name="edit_jobpost_qualification" id="edit_jobpost_qualification" class="form-control" data-validate="required" value="<?php echo $edit_jobpost_qualification;?>">
                </div>	
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="field-3" class="control-label">Candidate Experience</label>
                    <input type="text" name="edit_jobpost_experience" id="edit_jobpost_experience" class="form-control" data-validate="required" value="<?php echo $edit_jobpost_experience;?>">
                </div>	
            </div>                                                                        
        </div><!-- Second row Close-->

        <div class="row"><!-- Third row Start-->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Candidate Age Start</label>
                    <input type="text" name="edit_jobpost_candidate_age_start" id="edit_jobpost_candidate_age_start" class="form-control" value="<?php echo $edit_jobpost_candidate_age_start;?>">
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Candidate Age End</label>
                    <input type="text" name="edit_jobpost_candidate_age_end" id="edit_jobpost_candidate_age_end" class="form-control" value="<?php echo $edit_jobpost_candidate_age_end;?>">
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Salary Start Range ₹</label>
                    <input type="text" name="edit_jobpost_salary_start_range" id="edit_jobpost_salary_start_range" class="form-control" value="<?php echo $edit_jobpost_salary_start_range;?>">
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Salary End Range ₹</label>
                    <input type="text" name="edit_jobpost_salary_end_range" id="edit_jobpost_salary_end_range" class="form-control" value="<?php echo $edit_jobpost_salary_end_range;?>">
                </div>	
            </div>

            
        </div><!-- Third row Close-->
								
        <div class="row"><!-- Fourth row Start-->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-1" class="control-label">Gender</label>
                    <select name="edit_jobpost_gender" id="edit_jobpost_gender" class="round" data-validate="required">
                        <option value="<?php echo $edit_jobpost_gender; ?>"><?php echo $edit_jobpost_gender; ?></option>
                        <option value="Any Gender">Any Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>                                                              
                    </select>
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Shift time</label>
                    <select name="edit_jobpost_shift_time" id="edit_jobpost_shift_time" class="round" data-validate="required">
                        <option value="<?php echo $edit_jobpost_shift_time; ?>"><?php echo $edit_jobpost_shift_time; ?></option>
                        <option value="">Select Shift time</option>
                        <option value="Day Shift">Day Shift</option>
                        <option value="Mid Shift">Mid Shift</option>
                        <option value="Night Shift">Night Shift</option>                                                
                    </select>                                            
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Job Description</label>                                        
                    <textarea class="form-control" name="edit_jobpost_jobdescription" id="edit_jobpost_jobdescription" data-validate="required"><?php echo $edit_jobpost_jobdescription;?></textarea>
                </div>	
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="field-3" class="control-label">Other Information</label>                                        
                    <textarea class="form-control" name="edit_jobpost_otherinformation" id="edit_jobpost_otherinformation" ><?php echo $edit_jobpost_otherinformation;?></textarea>
                </div>	
            </div>
        </div><!-- Fourth row close-->
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Update</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>