```mermaid
---
config:
    layout: elk
---
graph
	subgraph loggedout [Logged out pages]
		Landing
		Login
		Register
	end

	subgraph loggedin [Logged in pages]
		Home
		Cart-.->Checkout
		Profile
		Logout
	end

	Logout-->loggedout
	Login-->loggedin
```
