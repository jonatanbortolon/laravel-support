export class RequestDetailComments {
  from: string;
  content: string;
  attachments: string[];
  date: string
  
  constructor(from: string, content: string, attachments: string[], date: string) {
    this.from = from;
    this.content = content;
    this.attachments = attachments;
    this.date = date;
  }
}