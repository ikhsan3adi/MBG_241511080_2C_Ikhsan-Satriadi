# JSON Request & Response Guide for this Project

## REQUEST

### Get Data

Method: GET

Query Parameters (optional):

| Parameter | Type   | Description                   |
| --------- | ------ | ----------------------------- |
| search    | string | Search term to filter results |

### Create Request

Method: POST

```json
{
    // example:
    "username": "", // string
    "email": "", // string
    "password": "", // string
    // other fields...
}
```

### Update Request

Method: PUT or PATCH

| Parameter | Type             | Description                  |
| --------- | ---------------- | ---------------------------- |
| id        | number or string | ID of the resource to update |

```json
{
    // example:
    "username": "", // string
    "email": "", // string
    "password": "", // string
    // other fields...
}
```

### Bulk Delete Request

Method: DELETE

```json
{
    "selected": [1, 2, "abc123"] // array of number or string to be deleted
}
```

## RESPONSE

### Login Response

status code: 200

```json
{
    "error": false, // boolean
    "message": "", // string
    "jwt_token": "", // string (optional, only for login)
    "redirect_url": "", // string (optional)
}
```

### Successful Response

status code: 200, 201

```json
{
    "error": false, // boolean
    "message": "", // string
    
    // object (optional)
    "data": {
        // example:
        "courses": [], // array of object (either object or array of objects)
        "user": {}, // object
    }

    // other fields (optional)
}
```

### Error Response

status code: 400, 401, 403, 404, 500

```json
{
    "error": true, // boolean
    "message": "", // string (optional) (either message or messages must be present)
    "messages": [] // array of string (optional)
}

```
