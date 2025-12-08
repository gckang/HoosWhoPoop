(function (global) {
    const { createElement: h, useState } = React;

    global.NavBar = function NavBar({ user, handleLogout }) {
        return h("header", { className: "app-header" }, 
            h("h1", null, "HoosWhoPoop"),
            h("p", null, "Hello pooper, ", h("span", { className: "username" }, user.username)),
            h("ul", { className: "navigation" },
                h("li", null, h("a", { href: "index.html" }, "Home")),
                h("li", null, h("a", { href: "rooms.html" }, "Rooms"))
            ),
            h("button", { className: "logout-button", onClick: handleLogout }, "Logout")
        );
    };
})(window);
