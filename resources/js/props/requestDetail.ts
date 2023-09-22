import { RequestState } from "@/types/requestState";

export class RequestDetail {
  id: string;
  title: string;
  state: RequestState;

  constructor(id: string, title: string, state: RequestState,) {
    this.id = id;
    this.title = title;
    this.state = state;
  }
}