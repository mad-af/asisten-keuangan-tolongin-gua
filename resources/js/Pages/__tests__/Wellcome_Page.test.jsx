import Page from "../Wellcome/Page.jsx";

test("Wellcome page exposes layout function", () => {
    expect(typeof Page).toBe("function");
    expect(typeof Page.layout).toBe("function");
    const wrapped = Page.layout(<div>child</div>);
    expect(wrapped.props.children.props.children).toBe("child");
});
