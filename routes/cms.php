<?php
// --------------------
// AUTHENTICATION
// --------------------
require __DIR__ . '/../middleware/auth.php';

switch ($methods) {

// --------------------
// READ CMS
// --------------------
    case 'GET':

        $results=$db->select("cms","*");

        $data=[];

         // i am using foreach loop to print all the results and save the data in $data array
        foreach ($results as $result) {
           $data[]= [
            
            'id'=>$result['id'],
            'status'=>$result['status']?? 1,
            'page_name'=>ucwords(trim($result['page_name']))?? "",
            'content'=>$result['content']?? "",
            'order'=>$result['order']?? 0,
            'slug_url'=>strtolower(trim($result['slug_url']))?? "",
            'position'=>strtolower(trim($result['position']))?? "",
            'parent_id'=>$result['parent_id']?? "",
            'removable'=>$result['removable']?? 1,
            'link_type'=>strtolower(trim($result['link_type']))?? "page",
            'target'=>strtolower(trim($result['target']))?? "_self",
            'external_url'=>trim($result['external_url'])?? NULL,
            'created_at'=>$result['created_at']?? NULL,
            'page_name_translations'=>$result['page_name_translations']?? NULL,
            'content_translations'=>$result['content_translations']?? NULL
        ];
        }

        if(!$data){
            http_response_code(404);
            echo json_encode([
                "status"=>false,
                "Message"=>"data miss while fetching"
            ]);
            exit;

        }

        http_response_code(200);
            echo json_encode([
                "status"=>true,
                "Message"=>"data  fetched successfully",
                "Data"=>$data
            ]);
            exit;


        break;
    
    // --------------------
    // CREATE CMS
    // --------------------
    case 'POST':
        // data coming through post method and properly arranged according to the requirement
            $status= 1;
        
            $page_name=ucwords(trim($_POST['page_name']))?? "";
            $content=$_POST['content']?? "";
            $order=$_POST['order']?? 0;
            $slug_url=strtolower(trim($_POST['slug_url']))?? "";
            $position=strtolower(trim($_POST['position']))?? "";
            $parent_id=$_POST['parent_id']?? "";
            $removable=$_POST['removable']?? 1;
            $link_type=strtolower(trim($_POST['link_type']))?? "page";
            $target=strtolower(trim($_POST['target']))?? "_self";
            $external_url=trim($_POST['external_url'])?? NULL;
            $created_at = date("Y-m-d");
            $page_name_translations=$_POST['page_name_translations']?? NULL;
            $content_translations=$_POST['content_translations']?? NULL;

        //---------------
        //INPUT VALIDATION
        //----------------

            if($page_name===0 || $content=== 0 || $order=== 0 || $slug_url=== 0 || $position=== 0 || $parent_id=== 0 ||  $link_type=== 0 || $target=== 0 || $external_url=== 0 ||  $page_name_translations=== 0 ||
            $content_translations=== 0 ){
                http_response_code();
                echo json_encode([
                    "status"=>false,
                    "Message"=>"All fields are required"
                ]);
                exit;
            }
            // checking slug url existance
            $slug_url_check=$db->get("cms","*",['slug_url'=>$slug_url]);
            
            if($slug_url_check){
                http_response_code(404);
                echo json_encode([
                    "status"=>false,
                    "Message"=>"slug url already exist"
                ]);
                exit;
            }

            $result=$db->insert("cms",[
                "status"=>$status,
                "content"=>$content,
                "page_name"=>$page_name,
                "order"=>$order,
                "slug_url"=>$slug_url,
                "position"=>$position,
                "parent_id"=>$parent_id,
                "removable"=>$removable,
                "link_type"=>$link_type,
                "target"=>$target,
                "external_url"=>$external_url,
                "created_at"=>$created_at,
                "page_name_translations"=>$page_name_translations,
                "content_translations"=>$content_translations
            ]);
        // if the addition of new cms fails then show the response
        if (!$result) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                'error' => "adding new cms error"
            ]);
            exit;
        }

        // if the addition of new cms adds successfully then show the reponse
        if ($result) {
    
            http_response_code(200);
            echo json_encode([
                "status" => true,
                'error' => "new CMS added successfully",
                "data" => $result
            ]);
            exit;
        }
        
        break;


            //---------------------
    // update airport
    //---------------------
    case "PUT":
        
        $input=json_decode(file_get_contents("php://input"),true);
        $cms_id=$input['id'];

        // it checks the id  and find the existance of the airport
        $exist_cms = $db->get("cms", "*", ['id' => $cms_id]);
        if (!$exist_cms) {
            http_response_code(401);
            echo json_encode(['error' => "Cms does not exist"]);
            exit;
        }
        //   i ma using $cmsdata array which stores the input fields  which can be used to updated 
        $cmsdata=[];

        if(isset($input['page_name'])){
            $cmsdata['page_name']=ucwords(trim($input['page_name']))?? "";}
        if(isset($input['content'])){
            $cmsdata['content']=$input['content']?? "";}
        if(isset($input['order'])){
            $cmsdata['order']=$input['order']?? 0;}
        if(isset($input['slug_url'])){
            $cmsdata['slug_url']=strtolower(trim($input['slug_url']))?? "";}
        if(isset($input['position'])){
            $cmsdata['position']=strtolower(trim($input['position']))?? "";}
        if(isset($input['parent_id'])){
            $cmsdata['parent_id']=$input['parent_id']?? "";}
        if(isset($input['removable'])){
            $cmsdata['removable']=$input['removable']?? 1;}
        if(isset($input['link_type'])){
            $cmsdata['link_type']=strtolower(trim($input['link_type']))?? "page";}
        if(isset($input['target'])){
            $cmsdata['target']=strtolower(trim($input['target']))?? "_self";}
        if(isset($input['external_url'])){
            $cmsdata['external_url']=trim($input['external_url'])?? NULL;}
        if(isset($input['created_at'])){
            $cmsdata['created_at']=date("Y-m-d");}
        if(isset($input['page_name_translations'])){
            $cmsdata['page_name_translations']=$input['page_name_translations']?? NULL;}
        if(isset($input['content_translations'])){
            $cmsdata['content_translations']=$input['content_translations']?? NULL;}


             //using medoo 

            
            $result=$db->update("cms",$cmsdata,['id'=>$cms_id]);

            if (!$result) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                'error' => "updating  cms error"
            ]);
            exit;
        }
        if ($result) {
    
            http_response_code(200);
            echo json_encode([
                "status" => true,
                'error' => " CMS updated successfully",
                "data" => $result
            ]);
            exit;
        }
        
        break;
    case "DELETE":
        $input=json_decode(file_get_contents("php://input"),true);
        $cms_id=$input['id'];
        $cms=$db->get('cms',"*",["id"=>$cms_id]);
      
        if(empty($cms)){
            http_response_code(404);
            echo json_encode([
                "status" => false,
                'error' => "cms id missing"
            ]);
            exit;
        }

        $result=$db->delete("cms",["id"=>$cms_id]);
         if (!$result) {
            http_response_code(401);
            echo json_encode([
                "status" => false,
                'error' => "deleting  cms error"
            ]);
            exit;
        }
        if ($result) {
    
            http_response_code(200);
            echo json_encode([
                "status" => true,
                'error' => " CMS deleted successfully",
                "data" => $result
            ]);
            exit;
        }
        break;
    default:
        # code...
        break;
}




?>