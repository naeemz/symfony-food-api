# Symfony Food API CRUD
Foods with multiple Categories can be add/update/delete/retrieve and sort by any column.

## Features:
- Create, Retrieve, Update, Delete (Soft Delete)
- Dynamic sorting i.e. You can sort by any field of any table (127.0.0.1:8000/api/food?name=desc)
- Dynamic Soft Delete option for Entity using Annotation i.e. user can specify using (annotation) that which column of Entity is for soft delete like **deleted_at** or **is_active** or **status**
- Validation using separate request rules class for each controller
- Foods can have multiple Categories and can be updated synchronously

> More awesome features can be added easily using same pattern used 👍 ✔


# Installation
```sh
clone https://github.com/naeemz/symfony-food-api.git
cd symfony-food-api
composer install
```
> Note: `Update .env file with DB info & include SITE_URL=`
```cmd
php bin/console doctrine:migrations:migrate
symfony server:start
127.0.0.1:8000
```
## API endpoints
```For example your app run on default port = 127.0.0.1:8000```

| Feature | HTTP Request | Endpoint | 
| ------ | ------ | ------ |
| All Food | GET | 127.0.0.1:8000/api/food |
| One Food | GET | 127.0.0.1:8000/api/food/1 |
| Add Food | POST | 127.0.0.1:8000/api/food |
| Update Food | POST | 127.0.0.1:8000/api/food/1/update |
| Delete Food | DELETE | 127.0.0.1:8000/api/food/1 |
| Sort Any Field | GET | 127.0.0.1:8000/api/food?created_at=desc |
| Sort Any Food | GET | 127.0.0.1:8000/api/food?name=asc |
>` Similar HTTP requests can be used for 127.0.0.1:8000/api/category`
## Json Data in HTTP requests

#### Add Food
```sh
POST 127.0.0.1:8000/api/food
{
    "name": "Pizza Spicy Large",
    "description": "Spicy Pizza with rich cheese and BBQ chicken",
    "price": "24",
    "serving_per_person": 2, //optional
    "image": File, //optional
    "categories": [1,3] //optional
}
```
#### Update Food
```sh
POST 127.0.0.1:8000/api/food/1
{
    "name": "Pizza Spicy Large",
    "description": "Spicy Pizza with rich cheese and BBQ chicken",
    "price": "24",
    "serving_per_person": 2, //optional
    "image": File, //optional
    "categories": [1,3] //optional
}
```
## License

MIT
