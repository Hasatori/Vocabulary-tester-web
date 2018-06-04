var BASE=$('base').attr("href");
function test(){
    $('.loaderWrapper').attr('style','display:block !important;');
      $.post(BASE+'test/test.php',{
     
          
     'test':'test'
          
    },function(data,textStatus,jqXHR){
         
       }
   ).done(function(data){ 
console.log(data);

    
          document.getElementById('result').innerHTML=data;
 $('.loaderWrapper').attr('style','display:none !important;');
   }); 
  
   
}
function loadBackup(){
    $('.loaderWrapper').attr('style','display:block !important;');
      $.post(BASE+'test/loadBackup.php',{
     
          
     'loadBackup':'loadBackup'
          
    },function(data,textStatus,jqXHR){
         
       }
   ).done(function(data){ 
console.log(data);

    
          document.getElementById('loadBackup').innerHTML=data;
 $('.loaderWrapper').attr('style','display:none !important;');
   }); 
  
}

function backup(){
      $('.loaderWrapper').attr('style','display:block !important;');
      $.post(BASE+'test/backup.php',{
     
          
     'backup':'backup'
          
    },function(data,textStatus,jqXHR){
         
       }
   ).done(function(data){ 
       document.location.reload();
   });  
}