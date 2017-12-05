<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Project; 
use App\TimeEntry;
use App\ExpenseEntry;
use App\Employee;

use App\Libraries\Mavenlink\MavenlinkApi;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $projects= Project::orderBy('id','DESC')->paginate(5);
        return view('Projects.index',compact('projects'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Projects.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'project_no' => 'required',
            'address' => 'required',
            'client_name' => 'required',
        ]);
        Project::create($request->all());
        return redirect()->route('projects.index')
                        ->with('success','Project created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project= Project::find($id);
        return view('Projects.show',compact('project'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project= Project::find($id);
        return view('Projects.edit',compact('project'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'project_no' => 'required',
            'address' => 'required',
            'client_name' => 'required',
        ]);
        Project::find($id)->update($request->all());
        return redirect()->route('projects.index')
                        ->with('success','Project updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Project::find($id)->delete();
        return redirect()->route('projects.index')
                        ->with('success','Project deleted successfully');
    }
    
    /**
   * Display homepage.
   *
   * @return Response
   */
  public function getProjectMavenlink()
  {
    $client = new MavenlinkApi('e6132990bac64a8f8af76917c9a9d8ec29b1721daeb65191b0b51e0151552f92');
    /** Getting the Workspace data **/
    $response = $client->getWorkspacesWithParam('include=custom_field_values');
    $result_set = json_decode($response,true);
    
    //echo '<pre>';
    //print_r($result_set);die;
    
    foreach($result_set['workspaces'] as $workspace_id=>$workspace){
        $result_arr=array();
           
        $project_title = $workspace['title'];
        $temp_arr = explode("-",$project_title);
        $project_no = trim($temp_arr[0]);
        unset($temp_arr[0]);
        $project_address = implode('-',$temp_arr);
        
        $client_name='';
        $client_address='';
        $project_no = '';
        $client_contact = '';
        $client_email = '';
        $new_or_repeat_client = '';
        
        if(!empty($workspace['custom_field_value_ids'])){
            foreach($workspace['custom_field_value_ids'] as $val){
                $custom_val = $result_set['custom_field_values'][$val];
                
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='Client Name')){
                    $client_name = $custom_val['display_value'];
                }
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='Client Address')){
                    $client_address = $custom_val['display_value'];
                }
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='Client Contact')){
                    $client_contact = $custom_val['display_value'];
                }
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='Client Email')){
                    $client_email = $custom_val['display_value'];
                }
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='New or Repeat Client')){
                    $new_or_repeat_client = $custom_val['display_value'];
                }
                if(!empty($custom_val) && (trim($custom_val['custom_field_name'])=='Project Number')){
                    $project_no = $custom_val['display_value'];
                }
                
            }
        }
        if(empty($project_no)){
            $project_title = $workspace['title'];
            $temp_arr = explode("-",$project_title);
            $project_no = trim($temp_arr[0]);
            unset($temp_arr);
        }
        if(empty($project_address)){
            $project_title = $workspace['title'];
            $temp_arr = explode("-",$project_title);
            $project_no = trim($temp_arr[0]);
            unset($temp_arr[0]);
            $project_address = implode('-',$temp_arr);
        }
        
        /** Project data Start*********************************************************************************/
        $result_arr['id'] = $workspace_id;
        $result_arr['project_no'] = $project_no;
        $result_arr['address'] = $project_address;
        $result_arr['client_name'] = $client_name;
        $result_arr['client_address'] = $client_address;
        $result_arr['title'] = $workspace['title'];
        $result_arr['start_date'] = $workspace['start_date'];
        $result_arr['updated_at'] = date("Y-m-d H:i:s",strtotime($workspace['updated_at']));
        $result_arr['created_at'] = date("Y-m-d H:i:s",strtotime($workspace['created_at']));
        $result_arr['status_msg'] = $workspace['status']['message'];
        $result_arr['total_expenses_in_cents'] = $workspace['total_expenses_in_cents'];
        $result_arr['price_in_cents'] = $workspace['price_in_cents'];
        $result_arr['new_or_repeat_client'] = $new_or_repeat_client;
        
        $result = Project::find($result_arr['id']);
        
        if (!empty($result->project_no)) {
            // output data of each row
            /** Update Section **/
            Project::find($result->id)->update($result_arr);
            /** Update Section End**/
            
        }else{
            /** Insert Section **/
            Project::create($result_arr);
            /** Insert Section End**/
            
        }
        /** Project data End **********************************************************************************/
        
        /** Time Entries **************************************************************************************/
    
        $time_response = $client->getAllTimeEntriesFromWorkspace($workspace_id);
        $time_result_set = json_decode($time_response,true);
        foreach($time_result_set['time_entries'] as $time_id=>$time_val){
            $time_arr = array();
            
            $time_arr['id']= $time_id;
            $time_arr['project_id']= $workspace_id;
            $time_arr['employee_id']= $time_val['user_id'];
            $time_arr['date_performed']= $time_val['date_performed'];
            $time_arr['notes']= $time_val['notes'];
            $time_arr['time_in_minutes']= $time_val['time_in_minutes'];
            $time_arr['rate_in_cents']= $time_val['rate_in_cents'];
            $time_arr['created_at']= date("Y-m-d H:i:s",strtotime($time_val['created_at']));
            $time_arr['updated_at']= date("Y-m-d H:i:s",strtotime($time_val['updated_at']));
            
            $result = TimeEntry::find($time_id);
            
            if (!empty($result->id)) {
                // output data of each row
                /** Update Section **/
                TimeEntry::find($time_id)->update($time_arr);
                
                /** Update Section End**/
                
            }else{
                /** Insert Section **/
                TimeEntry::create($time_arr);
                
                /** Insert Section End**/
                
            }
            
        }
        
        /** Time Entries End **********************************************************************************/
        
        /** Expenses Entries **************************************************************************************/
    
        $exp_response = $client->getAllExpensesFromWorkspace($workspace_id);
        $exp_result_set = json_decode($exp_response,true);
        foreach($exp_result_set['expenses'] as $exp_id=>$exp_val){
            $exp_arr = array();
            
            $exp_arr['id']= $exp_id;
            $exp_arr['project_id']= $workspace_id;
            $exp_arr['employee_id']= $exp_val['user_id'];
            $exp_arr['date']= $exp_val['date'];
            $exp_arr['notes']= $exp_val['notes'];
            $exp_arr['amount_in_cents']= $exp_val['amount_in_cents'];
            
            $exp_arr['created_at']= date("Y-m-d H:i:s",strtotime($exp_val['created_at']));
            $exp_arr['updated_at']= date("Y-m-d H:i:s",strtotime($exp_val['updated_at']));
            
            $result = ExpenseEntry::find($exp_id);
        
            if (!empty($result->id)) {
                // output data of each row
                /** Update Section **/
                ExpenseEntry::find($exp_id)->update($exp_arr);
                
                /** Update Section End**/
                
            }else{
                /** Insert Section **/
                ExpenseEntry::create($exp_arr);
                
                /** Insert Section End**/
                
            }
            
        }
    
    /** Expenses Entries End **********************************************************************************/

    } // Workspace Foreach end
    
    
    return redirect()->route('projects.index')
                    ->with('success','Project imported successfully');
    die;
  }
  
  public function getUsersMavenlink(){
    
    $client = new MavenlinkApi('e6132990bac64a8f8af76917c9a9d8ec29b1721daeb65191b0b51e0151552f92');
    /** User Entries **************************************************************************************/
    
    $user_response = $client->getUsers();
    $user_result_set = json_decode($user_response,true);
    //echo '<pre>';
    //print_r($user_result_set);die;
    
    foreach($user_result_set['users'] as $user_id=>$user_val){
        $user_arr = array();
        
        $user_arr['id']= $user_id;
        $user_arr['full_name']= $user_val['full_name'];
        $user_arr['email_address']= $user_val['email_address'];
        $user_arr['photo_path']= $user_val['photo_path'];
        
        $result = Employee::find($user_id);
        
    
        if (!empty($result->id)) {
            // output data of each row
            /** Update Section **/
            Employee::find($user_id)->update($user_arr);
            
            /** Update Section End**/
            
        }else{
            /** Insert Section **/
            Employee::create($user_arr);
            
            /** Insert Section End**/
            
        }
        
    }
    
    /** User Entries End **********************************************************************************/
    
  }
}
