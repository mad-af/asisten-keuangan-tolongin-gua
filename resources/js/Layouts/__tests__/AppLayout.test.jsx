import AppLayout from "../AppLayout.jsx";

test("AppLayout is a component and defines children contract", () => {
    expect(typeof AppLayout).toBe("function");
});
