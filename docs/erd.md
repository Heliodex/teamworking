```mermaid
erDiagram
	user {
		varchar(32) id PK
		datetime created
		text forename
		text surname
		text street
		text town
		text postcode
		integer category "0, 1, 2"
		text email UK
		text password
	}

	session {
		varchar(32) id PK
		datetime created
		varchar(32) userId FK
	}
	session }o--|| user: ""

	product {
		varchar(32) id PK
		datetime created
		text name
		text description
		integer price "as pence"
		integer stock ">= 0"
	}


	purchase {
		varchar(32) id PK
		datetime created
		varchar(32) userId FK
		varchar(32) productId FK
		integer quantity "> 0"
		boolean completed "0, 1"
	}

	purchase }o--|| user: ""
	purchase }o--|| product: ""
```
