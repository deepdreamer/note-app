`git config --local --add safe.directory /var/www/html`


```
curl -X POST   'http://localhost:8080/api/notes'   -H 'Content-Type: application/json'   -H 'Accept: application/json'   -d '{
"title": "Important meeting with client",
"content": "Discuss project timeline and next deliverables",
"priority": 4
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

```
curl -X GET \
  'http://localhost:8080/api/notes/{id}' \
  -H 'Accept: application/json'
```

```
curl -X PATCH \
  'http://localhost:8080/api/notes/{id}' \
  -H 'Content-Type: application/merge-patch+json' \
  -H 'Accept: application/json' \
  -d '{
    "priority": 33
  }'
```