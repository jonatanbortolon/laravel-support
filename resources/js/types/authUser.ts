import { UserRole } from "./userRole";

export type AuthUser = {
  id: string;
  name: string;
  email: string;
  role: UserRole;
}