import { render, screen } from "@testing-library/react";
import LoginLayout from "../LoginLayout.jsx";

test("renders child inside layout", () => {
  render(<LoginLayout><div data-testid="child">Hi</div></LoginLayout>);
  expect(screen.getByTestId("child").textContent).toBe("Hi");
});

