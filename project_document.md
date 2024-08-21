//// Setup for run project: 
1. clone the project from github :  https://github.com/developersksingh/dreamcast.git

2. create a database in withname  : dreamcast

3. php artisan migrate   		[ Migrate the existing migration ]

4. php artisan db:seed   		[generate the user role using seeder]

5. php artisan storage:link 	[Link the storage directory]

6. Some Important artisan command for clear cache : 
	php artisan cache:clear
	php artisan view:clear
	php artisan route:clear
	php artisan config:clear

7. php artisan serve  :  [Generate the project url]

Project : base URL :  http://localhost:8000

Web urls :  
			1. create a new user  :  http://localhost:8000/create-user
		    2. get the all users :  http://localhost:8000/users-data	

Api urls : 
        1. create a new user  :  http://localhost:8000/api/create-user
		2. get the all users :  http://localhost:8000/api//users-data
