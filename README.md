# ITSAR2 313 - SYSTEM INTEGRATION AND ARCHITECTURE 2
## LABORATORY REPORT: EXERCISE 1 MONOLITH VS MICROSERVICE

**Badajos, Math Auric Ros; Salazar, Shirley Ann; Villalobos, Jon Nathaniel; Yanson, Rea Nicole; Zambra, Maika**  
Information Technology, College of Computer Studies  
Carlos Hilado Memorial State University

## Abstract

This laboratory exercise implemented and compared two versions of the same Student Course System: a monolithic architecture and a microservices architecture. The monolithic version was built as a single Node.js and Express application containing all modules (students, courses, and enrollments) under one process, while the microservices version separated these modules into independent services with an API Gateway for unified access. Both implementations supported core operations such as CRUD for students and courses, enrollment creation, duplicate enrollment prevention, and seeded test data generation. Through implementation and endpoint testing, the monolithic architecture showed simpler deployment and easier local debugging, while the microservices architecture showed stronger modularity, better service isolation, and clearer scalability paths. However, microservices introduced added complexity in service startup, inter-service communication, and operational management.

## INTRODUCTION

Software architecture is a major determinant of a system's maintainability, scalability, fault tolerance, and operational cost. In this exercise, two common architectural styles were explored:

1. **Monolithic architecture**, where all business domains are packaged and deployed as one application.
2. **Microservices architecture**, where each domain capability is deployed as an independent service communicating over HTTP.

The exercise objective was to understand practical trade-offs rather than only theoretical differences. The same domain problem (Student Course System) was used to ensure fair comparison. In both implementations, the system handled students, courses, and enrollments with validation rules such as preventing duplicate enrollments and ensuring referential integrity.

Concepts applied in this lab included:

- RESTful API design (resource-based endpoints and HTTP methods).
- Separation of concerns (routes, controllers, models, middleware in monolith; service boundaries in microservices).
- API Gateway pattern for request routing in distributed systems.
- Inter-service communication and dependency handling (especially in Enrollment Service).
- Input validation, error handling, and health-check endpoints.

Requisites needed to attain the lab objectives were:

- Node.js runtime and npm.
- Express framework for service and API implementation.
- PowerShell scripts for dependency installation and multi-service startup.
- Basic HTTP testing using browser/curl and seed scripts.
- Knowledge of service ports and endpoint mapping.

## METHODOLOGY / IMPLEMENTATION

The implementation followed an iterative and comparative approach.

### 1. Monolithic System Implementation

- A single Express server was implemented on port 3000.
- Business modules for students, courses, and enrollments were organized using routes, controllers, and in-memory models.
- Middleware was added for request body validation and centralized error handling.
- Static frontend files were served from one public directory.
- A seed script inserted sample students, courses, and enrollment records for repeatable testing.

### 2. Microservices System Implementation

- Four independent Node.js services were prepared:
  - API Gateway (port 3000)
  - Student Service (port 3001)
  - Course Service (port 3002)
  - Enrollment Service (port 3003)
- PowerShell automation scripts were used:
  - `install-all.ps1` to install all service dependencies.
  - `start-all.ps1` to launch all services in separate terminals.
- The API Gateway proxied `/students`, `/courses`, and `/enrollments` routes to target services.
- The Enrollment Service performed inter-service HTTP calls to validate that student and course IDs exist before creating enrollments.
- Health-check endpoints were used to verify service availability.
- A separate microservices seed script populated distributed data and demonstrated service interactions.

### 3. Experimental Procedure

The following test workflow was applied to both architectures:

1. Start system services.
2. Run seed script to generate baseline test data.
3. Verify retrieval endpoints for students, courses, and enrollments.
4. Test invalid and duplicate enrollment cases.
5. Test delete operations and observe effect on related enrollments.
6. For microservices, observe gateway behavior when dependent service responses are delayed or unavailable.

## EXPERIMENTAL FINDINGS / OBSERVATIONS

Based on implementation and endpoint-level testing, the following observations were recorded:

1. Both architectures successfully executed functional requirements: creating students/courses, enrolling students, and listing records.
2. Duplicate enrollment prevention was enforced in both versions.
3. The monolithic version provided a faster setup path because only one process had to be started.
4. The microservices version required more startup coordination due to multiple services and ports.
5. In microservices, the Enrollment Service correctly depended on Student and Course services for validation, showing realistic distributed-system behavior.
6. The API Gateway provided a single client entry point, reducing client-side complexity.
7. Error handling in microservices was more explicit for distributed failures (timeouts and service unavailable scenarios), which is a key architectural advantage for operational visibility.
8. The project demonstrated that microservices improve modularity and independent deployment options, but increase operational overhead.

## LAB DISCUSSIONS / COMPUTATIONS

The main discussion centered on comparing architectural qualities using observed behavior.

### Comparative Analysis

| Metric | Monolithic | Microservices |
|---|---|---|
| Deployment unit | Single application | Multiple independent services |
| Startup complexity | Low | High |
| Local development setup | Simple | More complex |
| Fault isolation | Limited (shared process) | Better (service-level isolation) |
| Scalability model | Scale whole app | Scale selected services |
| Service communication overhead | Minimal (in-process calls) | Present (HTTP network calls) |
| Operational observability needs | Moderate | Higher |

### Interpretation of Results

- The monolithic architecture is efficient for small systems, quick prototyping, and teams needing simpler deployment.
- The microservices architecture is beneficial when modular scaling, team autonomy, and independent service evolution are priorities.
- The exercise confirms that architecture choice should align with system size, team structure, and operational maturity.

No numerical performance benchmark was formally instrumented in this activity; thus, computations are qualitative and based on implementation complexity, runtime behavior, and service interaction patterns observed during tests.

## CONCLUSIONS

The laboratory achieved its objective of implementing and comparing monolithic and microservices architectures using the same Student Course System domain. The monolithic implementation was easier to build, run, and debug in a classroom setting, making it suitable for smaller and less complex systems. The microservices implementation introduced additional setup and communication overhead but provided clearer separation of concerns, better fault isolation, and stronger long-term scalability potential. Therefore, both architectures are valid depending on context: monoliths are practical for simplicity and rapid delivery, while microservices are appropriate for systems requiring modular growth and independent service management.

## References

1. Project source code and documentation, *Lab_Activity_1* workspace.
2. Monolithic Architecture README, Student Course System documentation.
3. Microservices Architecture README, Student Course System documentation.
4. Node.js Documentation. https://nodejs.org/docs/
5. Express.js Documentation. https://expressjs.com/
6. Newman, S. (2021). *Building Microservices* (2nd ed.). O'Reilly Media.
