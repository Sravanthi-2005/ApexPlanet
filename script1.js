function togglePassword(id){

let x=document.getElementById(id);

if(x.type==="password")
x.type="text";
else
x.type="password";

}

let form=document.getElementById("registerForm");

if(form){

const existingEmails=[
"admin@gmail.com",
"test@gmail.com",
"user@gmail.com"
];

document.getElementById("email").addEventListener("blur",function(){

let email=this.value;

let msg=document.getElementById("emailMsg");

if(existingEmails.includes(email)){

msg.style.color="red";

msg.innerHTML="Email already exists";

}

else{

msg.style.color="green";

msg.innerHTML="Email Available";

}

});

form.addEventListener("submit",function(e){

let p=document.getElementById("password").value;

let cp=document.getElementById("confirmPassword").value;

let message=document.getElementById("message");

if(p!==cp){

e.preventDefault();

message.style.color="red";

message.innerHTML="Passwords do not match";

}

else{

message.style.color="green";

message.innerHTML="Registration Successful";

}

});

}