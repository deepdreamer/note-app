# Basic api for task managing app
Simple api for storing notes. Notes have name, content and priority.

App was tested on linux:
Ubuntu 22.04.5 LTS, Linux version 5.15.167.4-microsoft-standard-WSL2

Docker is needed to run this project.

Documentation is available on:
http://localhost:8080/api/docs

### instalation steps:
1. rename file `.evn.test.local.template` to `.env.test.local`
2. rename file .`env.local.template` to `.env.local`
3. run `make install-project`
4. run `make database-set-up`

### other commands:
`make start-project` -- just starts up the project

`make restart-project` -- stops and recreates containeres

`make rebuild-project` -- same as `make restart-project` but also rebuilds containers

`make run-tests-of-endpoints` -- runs application tests to make sure endpoints work as expected

### linting commands
`make lint` -- phpcs, `make lint-fix` --phpcbf, `make link-phpstan` --phpstan

You can create note: 
```
curl -X POST   'http://localhost:8080/api/notes'   -H 'Content-Type: application/json'   -H 'Accept: application/json'   -d '{
"title": "Important meeting with client",
"content": "Discuss project timeline and next deliverables",
"priority": 4
}'
```



get note:

```
curl -X GET \
  'http://localhost:8080/api/notes/{id}' \
  -H 'Accept: application/json'
```
update note:
```
curl -X PATCH \
  'http://localhost:8080/api/notes/{id}' \
  -H 'Content-Type: application/merge-patch+json' \
  -H 'Accept: application/json' \
  -d '{
    "priority": 33
  }'
```

```
curl -X PUT \
  'http://localhost:8080/api/notes/16' \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "title": "Updated meeting with client",
    "content": "Revised agenda for project timeline discussion",
    "priority": 5
  }'
```