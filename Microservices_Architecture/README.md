# Microservices Architecture - Student Course System

This is the **Microservices** implementation of the Student Course System for Laboratory 1.


## Services

| Service | Port | Description |
|---------|------|-------------|
| API Gateway | 3000 | Unified entry point, routes to services |
| Student Service | 3001 | Manages student CRUD operations |
| Course Service | 3002 | Manages course CRUD operations |
| Enrollment Service | 3003 | Manages enrollments, validates via other services |

## Quick Start

### 1. Install Dependencies (for each service)

```bash
# In each service directory
cd student-service && npm install
cd ../course-service && npm install
cd ../enrollment-service && npm install
cd ../api-gateway && npm install
```

Or run all at once from the Microservices_Architecture folder:

```powershell
# Windows PowerShell
cd student-service; npm install; cd ..
cd course-service; npm install; cd ..
cd enrollment-service; npm install; cd ..
cd api-gateway; npm install; cd ..
```

### 2. Start All Services (in separate terminals)

**Terminal 1 - Student Service:**
```bash
cd student-service
npm start
```

**Terminal 2 - Course Service:**
```bash
cd course-service
npm start
```

**Terminal 3 - Enrollment Service:**
```bash
cd enrollment-service
npm start
```

**Terminal 4 - API Gateway (Optional but recommended):**
```bash
cd api-gateway
npm start
```

### 3. Seed Sample Data

```bash
# From Microservices_Architecture folder
node seed.js
```



## Testing the API

### Create a Student
```bash
curl -X POST http://localhost:3000/students \
  -H "Content-Type: application/json" \
  -d '{"fullName": "John Doe", "email": "john@email.com", "age": 20}'
```

### Create a Course
```bash
curl -X POST http://localhost:3000/courses \
  -H "Content-Type: application/json" \
  -d '{"name": "Web Dev 101", "description": "Intro to web development", "credits": 3}'
```
### Create an Enrollment
```bash
curl -X POST http://localhost:3000/enrollments \
  -H "Content-Type: application/json" \
  -d '{"studentId": "<student-id>", "courseId": "<course-id>"}'
```

## Project Structure
```
Microservices_Architecture/
├── api-gateway/
│   ├── package.json
│   ├── server.js
│   └── public/
│       ├── index.html
│       ├── script.js
│       └── styles.css
├── student-service/
│   ├── package.json
│   └── server.js
├── course-service/
│   ├── package.json
│   └── server.js
├── enrollment-service/
│   ├── package.json
│   └── server.js
├── seed.js
└── README.md
```


