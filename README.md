## ITSAR2 313 - SYSTEM INTEGRATION AND ARCHITECTURE 2  
## LABORATORY REPORT: EXERCISE 2 MONOLITH VS MICROSERVICE  
**Badajos, Math Auric Ros; Salazar, Shirley Ann; Villalobos, Jon Nathaniel; Yanson, Rea Nicole; Zambra, Maika**
Information Technology, College of Computer Studies  
Carlos Hilado Memorial State University

## Abstract

This laboratory exercise implemented and tested a Student Course System using a microservices architecture, then analyzed its behavior against monolithic design principles in terms of modularity, deployment, fault isolation, and operational complexity. The system was divided into Student Service, Course Service, Enrollment Service, and an API Gateway, with curl-based experiments focused on normal operations and edge cases such as invalid requests (400), missing resources (404), duplicate data conflicts (409), unavailable dependencies (503), and timeout scenarios (504). Results showed that microservices improve service-level independence and fault containment compared to monoliths, but they also introduce distributed-system concerns such as inter-service latency, timeout management, and stricter API contract consistency. Overall, the exercise demonstrated that architecture choice is context-driven: monoliths are simpler for smaller systems, while microservices provide greater scalability and maintainability for larger, evolving applications when supported by disciplined testing and integration patterns.

## I. INTRODUCTION

Modern software architecture commonly follows two major styles: monolithic architecture and microservices architecture. In a monolith, all modules (student, course, and enrollment features) run as one deployable unit and usually share one process and codebase. This style is straightforward to build and debug for small systems, but as requirements grow, tight coupling can make scaling and maintenance difficult. In contrast, microservices split the system into independent services, each with a focused responsibility and its own API. This decomposition supports independent updates, better fault isolation, and technology flexibility.

For this exercise, the objective was to apply microservice principles in a practical student-course enrollment domain while comparing the observed behavior to monolithic expectations. Key concepts used include service decomposition, RESTful API communication, API gateway routing, input validation, standardized error contracts, and resiliency controls (timeouts and failure handling). To satisfy the exercise objectives, the implementation required: (1) separate service processes with clear responsibilities, (2) inter-service communication for enrollment validation, (3) consistent HTTP status usage, and (4) repeatable verification using curl-based test cases and documented evidence.

## II. METHODOLOGY / IMPLEMENTATION

The implementation followed an incremental integration approach:

1. Service decomposition and setup
- Student Service (port 3001): manages student CRUD operations.
- Course Service (port 3002): manages course CRUD operations.
- Enrollment Service (port 3003): creates and manages enrollments while validating studentId and courseId through dependent services.
- API Gateway (port 3000): provides a unified entry point for clients.

2. API and validation design
- Required fields were enforced for each resource (for example: fullName/email/age for students; name/description/credits for courses).
- Common error body format was used: {"error": "ERROR_CODE", "message": "Human readable explanation"}.
- Standard HTTP semantics were mapped to behavior: 400, 404, 409, 503, and 504.

3. Data seeding and controlled test execution
- Dependencies were installed per service.
- Services were launched in separate terminals to mimic independent runtime contexts.
- Seed data was generated using seed.js to support deterministic tests.
- Test procedures were executed through predefined curl commands from tests/curl-tests.md.

4. Edge-case simulation for distributed behavior
- 503 scenarios were tested by intentionally stopping dependency services.
- 504 scenarios were tested by reducing timeout thresholds (SERVICE_TIMEOUT) and observing aborted dependency calls.
- Each run captured status line, headers, and JSON body for evidence documentation.

5. Documentation and verification
- Output artifacts were stored in docs/evidence/*.txt.
- Results were matched against expected status codes and error messages.
- Observations were consolidated into this report to evaluate architectural outcomes.

## III. EXPERIMENTAL FINDINGS / OBSERVATIONS

The experiments confirmed that the implemented services return correct responses for both successful and failing requests.

1. Successful operations
- Student, course, and enrollment creation worked correctly under valid input.
- Retrieval endpoints returned expected data lists and item records.

2. Validation and client errors
- 400 Bad Request occurred for missing/invalid fields such as missing fullName, invalid email, invalid age, missing course name, missing studentId, and empty request body.
- 404 Not Found occurred for non-existent records and invalid routes.
- 409 Conflict occurred for duplicate student emails and duplicate enrollments.

3. Distributed failure behavior
- 503 Service Unavailable was observed when Enrollment Service could not reach Student or Course Service.
- 504 Gateway Timeout was observed when dependency response exceeded configured timeout.

4. Consistency observations
- Error responses were generally consistent with predefined error codes.
- The API gateway and services showed predictable behavior under repeated curl trials.
- Evidence files show expected outcomes for key test cases:
  - 201_student_created.txt, 201_course_created.txt, 201_enrollment_created.txt
  - 400_missing_fullname.txt, 400_invalid_email.txt, 400_invalid_age.txt, 400_empty_body.txt
  - 404_student_not_found.txt, 404_course_not_found.txt, 404_invalid_route.txt
  - 409_duplicate_email.txt, 409_duplicate_enrollment.txt
  - 503_student_service_down.txt, 504_gateway_timeout.txt

## IV. LAB DISCUSSIONS / COMPUTATIONS

This section interprets the observed outcomes and compares monolith and microservice implications.

1. Architectural trade-off analysis
- Monolith advantage: easier setup, simpler local debugging, and fewer moving parts.
- Monolith limitation: shared deployment cycle and reduced fault isolation.
- Microservice advantage: domain-focused services, independent scaling/deployment, and clearer service ownership.
- Microservice limitation: network overhead, dependency failures, and higher operational complexity.

2. Error-code distribution insight
- Core client-side robustness is reflected in frequent 400/404/409 handling.
- Resilience checks (503/504) prove that the system accounts for real-world network/service instability.
- If total tests are denoted as N and failed/edge-case tests as E, then edge-case coverage ratio is:

  Coverage = (E / N) x 100%

  Based on the documented evidence set, all targeted error families were represented (400, 404, 409, 503, 504), indicating complete requirement-level scenario coverage for this laboratory scope.

3. Reliability and maintainability discussion
- The implementation demonstrates practical defensive programming through pre-validation and downstream verification.
- Timeout control prevents indefinite request blocking, improving service responsiveness.
- Consistent error contracts simplify client-side handling and test automation.

4. Practical lessons learned
- Integration tests are essential in distributed systems because correctness depends on both local logic and service availability.
- Architecture quality is measured not only by happy-path success but also by failure predictability and recovery behavior.

## V. CONCLUSIONS

The laboratory objectives were achieved by building and validating a functional microservices-based Student Course System and comparing its behavior with monolithic architecture concepts. Experimental results verified correct handling of standard client errors (400, 404, 409) and distributed-system failures (503, 504), with reproducible evidence from curl outputs. Compared with a monolith, the microservices solution provided stronger modularity and fault isolation but required additional mechanisms for communication reliability, timeout governance, and operational coordination. Therefore, microservices are advantageous for scalable and evolving systems when supported by consistent API design and robust edge-case testing, while monoliths remain practical for simpler and tightly scoped applications.

## References

1. [README.md](README.md)
2. [docs/report.md](docs/report.md)
3. [tests/curl-tests.md](tests/curl-tests.md)
4. [docker-compose.yml](docker-compose.yml)
5. [seed.js](seed.js)
6. [student-service/server.js](student-service/server.js)
7. [course-service/server.js](course-service/server.js)
8. [enrollment-service/server.js](enrollment-service/server.js)
9. [api-gateway/server.js](api-gateway/server.js)
10. [docs/evidence/README.md](docs/evidence/README.md)
11. [docs/evidence/201_student_created.txt](docs/evidence/201_student_created.txt)
12. [docs/evidence/201_course_created.txt](docs/evidence/201_course_created.txt)
13. [docs/evidence/201_enrollment_created.txt](docs/evidence/201_enrollment_created.txt)
14. [docs/evidence/400_missing_fullname.txt](docs/evidence/400_missing_fullname.txt)
15. [docs/evidence/400_invalid_email.txt](docs/evidence/400_invalid_email.txt)
16. [docs/evidence/400_invalid_age.txt](docs/evidence/400_invalid_age.txt)
17. [docs/evidence/400_missing_course_name.txt](docs/evidence/400_missing_course_name.txt)
18. [docs/evidence/400_missing_studentId.txt](docs/evidence/400_missing_studentId.txt)
19. [docs/evidence/400_empty_body.txt](docs/evidence/400_empty_body.txt)
20. [docs/evidence/404_student_not_found.txt](docs/evidence/404_student_not_found.txt)
21. [docs/evidence/404_course_not_found.txt](docs/evidence/404_course_not_found.txt)
22. [docs/evidence/404_update_nonexistent_student.txt](docs/evidence/404_update_nonexistent_student.txt)
23. [docs/evidence/404_delete_nonexistent_course.txt](docs/evidence/404_delete_nonexistent_course.txt)
24. [docs/evidence/404_enroll_nonexistent_student.txt](docs/evidence/404_enroll_nonexistent_student.txt)
25. [docs/evidence/404_enroll_nonexistent_course.txt](docs/evidence/404_enroll_nonexistent_course.txt)
26. [docs/evidence/404_invalid_route.txt](docs/evidence/404_invalid_route.txt)
27. [docs/evidence/409_duplicate_email.txt](docs/evidence/409_duplicate_email.txt)
28. [docs/evidence/409_duplicate_enrollment.txt](docs/evidence/409_duplicate_enrollment.txt)
29. [docs/evidence/503_student_service_down.txt](docs/evidence/503_student_service_down.txt)
30. [docs/evidence/504_gateway_timeout.txt](docs/evidence/504_gateway_timeout.txt)
