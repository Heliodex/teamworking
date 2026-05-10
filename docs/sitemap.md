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
		Orders<-.->Cart-.->Checkout
		Profile
		Admin
		Logout
	end

	Logout-->Login
	Login-->Home
```
