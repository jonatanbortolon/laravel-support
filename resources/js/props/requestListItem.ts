import { RequestState } from "@/types/requestState";

export class RequestListItem {
  id: string;
  title: string;
  state: RequestState;
  date: string;
  from: {
    id: string;
    name: string;
  };

  constructor(
    id: string,
    title: string,
    state: RequestState,
    date: string,
    from: {
      id: string;
      name: string;
    }
  ) {
    this.id = id;
    this.title = title;
    this.state = state;
    this.date = date;
    this.from = from;
  }
}