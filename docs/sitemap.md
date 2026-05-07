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
		Admin
		Logout
	end

	Logout-->Login
	Login-->Home
```
