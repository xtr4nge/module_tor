<b>tor</b> is a connection-oriented anonymizing communication service. Users choose a source-routed path through a set of nodes, and negotiate a "virtual circuit" through the network, in which each node knows its predecessor and successor, but no others. Traffic flowing down the circuit is unwrapped by a symmetric key at each node, which reveals the downstream node.
<br>
<br>Basically tor provides a distributed network of servers ("onion routers"). Users bounce their TCP streams — web traffic, ftp, ssh, etc — around the routers, and recipients, observers, and even the routers themselves have difficulty tracking the source of the stream.
<br>
<br>https://www.torproject.org/
<br>
<br><b>Authors: </b>Roger Dingledine [arma at mit.edu], Nick Mathewson [nickm at alum.mit.edu]
