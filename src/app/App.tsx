import { RouterProvider, createBrowserRouter } from "react-router";
import Home from "./pages/Home";
import AdminLogin from "./pages/AdminLogin";
import AdminDashboard from "./pages/AdminDashboard";

const router = createBrowserRouter([
  { path: "/", Component: Home },
  { path: "/admin", Component: AdminLogin },
  { path: "/admin/dashboard", Component: AdminDashboard },
]);

export default function App() {
  return <RouterProvider router={router} />;
}
