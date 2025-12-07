import { test, expect } from "bun:test";
import Page from "../Chat/Index.jsx";

test("Chat Index is a component", () => {
    expect(typeof Page).toBe("function");
});
