try {
    const { Window } = await import("happy-dom");
    const win = new Window();
    // ensure body exists
    win.document.write(
        "<!DOCTYPE html><html><head></head><body></body></html>"
    );
    globalThis.window = win;
    globalThis.document = win.document;
    globalThis.navigator = win.navigator;
} catch (e) {
    globalThis.document = globalThis.document || {
        body: {
            appendChild: (el) => el,
        },
        createElement: (name) => ({
            nodeName: name,
            setAttribute: () => {},
            appendChild: () => {},
        }),
    };
    globalThis.window = globalThis.window || {
        document: globalThis.document,
        location: { href: "http://localhost" },
    };
    globalThis.navigator = globalThis.navigator || { userAgent: "bun-test" };
}
